<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Components\SBP\FaqReader;
use FroshPluginUploader\Structs\Plugin;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;

class PluginUpdater
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var CommonMarkConverter
     */
    private $markdownParser;

    public function __construct(Client $client)
    {
        $this->client = $client;

        $environment = Environment::createCommonMarkEnvironment();
        $config = [
            'external_link' => [
                'internal_hosts' => '/(^|.)shopware.com/',
                'open_in_new_window' => true,
                'noreferrer' => 'external',
            ],
        ];
        $environment->addExtension(new DisallowedRawHtmlExtension());
        $environment->addExtension(new ExternalLinkExtension());

        $this->markdownParser = new CommonMarkConverter($config, $environment);
    }

    public function sync(PluginInterface $plugin, Plugin $storePlugin): void
    {
        $resourcesFolderPath = $plugin->getResourcesFolderPath();

        foreach ($storePlugin->infos as &$infoTranslation) {
            $language = mb_substr($infoTranslation->locale->name, 0, 2);
            $languageFile = $resourcesFolderPath . '/' . $language . '.html';
            $languageFileMarkdown = $resourcesFolderPath . '/' . $language . '.md';
            $languageManualFile = $resourcesFolderPath . '/' . $language . '_manual.html';
            $languageManualFileMarkdown = $resourcesFolderPath . '/' . $language . '_manual.md';
            $languageHighlightsFile = $resourcesFolderPath . '/' . $language . '_highlights.txt';
            $languageFeaturesFile = $resourcesFolderPath . '/' . $language . '_features.txt';
            $languageFaqFile = $resourcesFolderPath . '/' . $language . '_faq.md';

            if (file_exists($languageFile)) {
                $infoTranslation->description = $this->convertDescription(file_get_contents($languageFile));
            }

            if (file_exists($languageFileMarkdown)) {
                $infoTranslation->description = $this->convertDescription($this->markdownParser->convertToHtml(file_get_contents($languageFileMarkdown)));
            }

            $infoTranslation->installationManual = '';

            if (file_exists($languageManualFile)) {
                $infoTranslation->installationManual = $this->convertDescription(file_get_contents($languageManualFile));
            }

            if (file_exists($languageManualFileMarkdown)) {
                $infoTranslation->installationManual = $this->convertDescription($this->markdownParser->convertToHtml(file_get_contents($languageManualFileMarkdown)));
            }

            if (file_exists($languageHighlightsFile)) {
                $infoTranslation->highlights = file_get_contents($languageHighlightsFile);
            }

            if (file_exists($languageFeaturesFile)) {
                $infoTranslation->features = file_get_contents($languageFeaturesFile);
            }

            if (file_exists($languageFaqFile)) {
                $infoTranslation->faqs = (new FaqReader())->parseFaq($languageFaqFile);
            }

            if ($language === 'de') {
                if ($plugin->getReader()->getDescriptionGerman()) {
                    $infoTranslation->shortDescription = $plugin->getReader()->getDescriptionGerman();
                }

                $infoTranslation->name = $plugin->getReader()->getLabelGerman();
            } else {
                if ($plugin->getReader()->getDescriptionEnglish()) {
                    $infoTranslation->shortDescription = $plugin->getReader()->getDescriptionEnglish();
                }

                $infoTranslation->name = $plugin->getReader()->getLabelEnglish();
            }
        }

        unset($infoTranslation);

        if (count($storePlugin->localizations) < 2) {
            $this->addDefaultLocales($storePlugin);
        }

        $this->setLicense($storePlugin, $plugin->getReader()->getLicense());

        if ($plugin->hasStoreJson()) {
            $plugin->getStoreJson()->applyToPlugin($storePlugin, $this->client->General()->all());
        }

        $this->client->Plugins()->put($storePlugin->id, $storePlugin);

        if (file_exists($resourcesFolderPath . '/icon.png')) {
            $this->client->Plugins()->addIcon($storePlugin->id, $resourcesFolderPath . '/icon.png');
        }

        $imageDir = $resourcesFolderPath . '/images';

        if (file_exists($imageDir)) {
            $images = $this->client->Plugins()->getImages($storePlugin->id);

            foreach ($images as $image) {
                $this->client->Plugins()->deleteImage($storePlugin->id, $image->id);
            }

            foreach (scandir($imageDir, \SCANDIR_SORT_ASCENDING) as $image) {
                if ($image[0] === '.') {
                    continue;
                }

                $storeImage = $this->client->Plugins()->addImage($storePlugin->id, $imageDir . '/' . $image);

                if ($plugin->hasStoreJson() && $plugin->getStoreJson()->applyImageUpdate($storeImage, $image)) {
                    $this->client->Plugins()->updateImage($storePlugin->id, $storeImage);
                }
            }
        }
    }

    private function addDefaultLocales(Plugin $plugin): void
    {
        $allLocales = $this->client->General()->getLocalizations();
        $plugin->localizations = [];

        foreach ($allLocales as $allLocale) {
            $shortLocale = mb_substr($allLocale['name'], 0, 2);
            if ($shortLocale === 'de' || $shortLocale === 'en') {
                $plugin->localizations[] = $allLocale;
            }
        }
    }

    private function setLicense(Plugin $plugin, string $license): void
    {
        $license = mb_strtolower($license);
        $availableStoreLicenses = $this->client->General()->all()['licenses'];

        $availableLicenses = array_column($availableStoreLicenses, 'name');

        if (!in_array($license, $availableLicenses, true)) {
            $license = 'open_source';
        }

        foreach ($availableStoreLicenses as $licenseItem) {
            if ($licenseItem['name'] === $license) {
                $plugin->license = $licenseItem;
            }
        }
    }

    private function convertDescription(string $content): string
    {
        return strip_tags($content, '<a><b><i><em><strong><ul><ol><li><p><br><h2><h3><h4>');
    }
}
