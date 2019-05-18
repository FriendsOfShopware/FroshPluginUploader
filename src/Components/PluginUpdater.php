<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Structs\Plugin;

class PluginUpdater
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var \Parsedown
     */
    private $markdownParser;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->markdownParser = new \Parsedown();
    }

    public function sync(PluginInterface $plugin): void
    {
        $pluginId = (int) Util::getEnv('PLUGIN_ID');

        $pluginInformation = $this->client->Plugins()->get($pluginId);
        $resourcesFolderPath = $plugin->getResourcesFolderPath();

        foreach ($pluginInformation->infos as &$infoTranslation) {
            $language = substr($infoTranslation->locale->name, 0, 2);
            $languageFile = $resourcesFolderPath . '/' . $language . '.html';
            $languageFileMarkdown = $resourcesFolderPath . '/' . $language . '.md';
            $languageManualFile = $resourcesFolderPath . '/' . $language . '_manual.html';
            $languageManualFileMarkdown = $resourcesFolderPath . '/' . $language . '_manual.md';
            $languageHighlightsFile = $resourcesFolderPath . '/' . $language . '_highlights.txt';
            $languageFeaturesFile = $resourcesFolderPath . '/' . $language . '_features.txt';

            if (file_exists($languageFile)) {
                $infoTranslation->description = $this->convertDescription(file_get_contents($languageFile));
            }

            if (file_exists($languageFileMarkdown)) {
                $infoTranslation->description = $this->convertDescription($this->markdownParser->parse(file_get_contents($languageFileMarkdown)));
            }

            $infoTranslation->installationManual = '';

            if (file_exists($languageManualFile)) {
                $infoTranslation->installationManual = $this->convertDescription(file_get_contents($languageManualFile));
            }

            if (file_exists($languageManualFileMarkdown)) {
                $infoTranslation->installationManual = $this->convertDescription($this->markdownParser->parse(file_get_contents($languageManualFileMarkdown)));
            }

            if (file_exists($languageHighlightsFile)) {
                $infoTranslation->highlights = file_get_contents($languageHighlightsFile);
            }

            if (file_exists($languageFeaturesFile)) {
                $infoTranslation->features = file_get_contents($languageFeaturesFile);
            }

            if ($language === 'de') {
                $infoTranslation->shortDescription = $plugin->getReader()->getDescriptionGerman();
                $infoTranslation->name = $plugin->getReader()->getLabelGerman();
            } else {
                $infoTranslation->shortDescription = $plugin->getReader()->getDescriptionEnglish();
                $infoTranslation->name = $plugin->getReader()->getLabelEnglish();
            }
        }

        unset($infoTranslation);

        if (file_exists($resourcesFolderPath . '/icon.png')) {
            $this->client->Plugins()->addIcon($pluginId, $resourcesFolderPath . '/icon.png');
        }

        if (count($pluginInformation->localizations) < 2) {
            $this->addDefaultLocales($pluginInformation);
        }

        $this->setLicense($pluginInformation, $plugin->getReader()->getLicense());

        $plugin->getStoreJson()->applyToPlugin($pluginInformation, $this->client->General()->all());

        $this->client->Plugins()->put($pluginId, $pluginInformation);

        $imageDir = $resourcesFolderPath . '/images';

        if (file_exists($imageDir)) {
            $images = $this->client->Plugins()->getImages($pluginId);

            foreach ($images as $image) {
                $this->client->Plugins()->deleteImage($pluginId, $image->id);
            }

            foreach (scandir($imageDir, SCANDIR_SORT_ASCENDING) as $image) {
                if ($image[0] === '.') {
                    continue;
                }

                $this->client->Plugins()->addImage($pluginId, $imageDir . '/' . $image);
            }
        }
    }

    private function addDefaultLocales(Plugin $plugin)
    {
        $allLocales = $this->client->General()->getLocalizations();
        $plugin->localizations = [];

        foreach ($allLocales as $allLocale) {
            $shortLocale = substr($allLocale['name'], 0, 2);
            if ($shortLocale === 'de' || $shortLocale === 'en') {
                $plugin->localizations[] = $allLocale;
            }
        }
    }

    private function setLicense(Plugin $plugin, string $license)
    {
        $availableLicenses = $this->client->General()->all()['licenses'];
        foreach ($availableLicenses as $licenseItem) {
            if ($licenseItem['name'] === $license) {
                $plugin->license = $licenseItem;
                return;
            }
        }

        throw new \RuntimeException(sprintf('Invalid license given "%s". Following are available %s', $license, implode(',', array_column($availableLicenses, 'name'))));
    }

    private function convertDescription(string $content): string
    {
        return strip_tags($content, '<a><b><i><em><strong><ul><ol><li><p><br><h2><h3><h4>');
    }
}
