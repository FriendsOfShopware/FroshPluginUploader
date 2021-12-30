<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use function count;
use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Components\SBP\FaqReader;
use FroshPluginUploader\Structs\License;
use FroshPluginUploader\Structs\Plugin;
use function in_array;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\MarkdownConverter;
use const SCANDIR_SORT_ASCENDING;

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

        $config = [
            'external_link' => [
                'internal_hosts' => '/(^|.)shopware.com/',
                'open_in_new_window' => true,
                'noreferrer' => 'external',
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DisallowedRawHtmlExtension());
        $environment->addExtension(new ExternalLinkExtension());

        $this->markdownParser = new MarkdownConverter($environment);
    }

    public function sync(PluginInterface $plugin, Plugin $storePlugin): void
    {
        $resourcesFolderPath = $plugin->getResourcesFolderPath();

        foreach ($storePlugin->infos as $infoTranslation) {
            $language = mb_substr($infoTranslation->locale->name, 0, 2);
            $languageFile = $resourcesFolderPath . '/' . $language . '.html';
            $languageFileMarkdown = $resourcesFolderPath . '/' . $language . '.md';
            $languageManualFile = $resourcesFolderPath . '/' . $language . '_manual.html';
            $languageManualFileMarkdown = $resourcesFolderPath . '/' . $language . '_manual.md';
            $languageHighlightsFile = $resourcesFolderPath . '/' . $language . '_highlights.txt';
            $languageFeaturesFile = $resourcesFolderPath . '/' . $language . '_features.txt';
            $languageFaqFile = $resourcesFolderPath . '/' . $language . '_faq.md';

            if (is_file($languageFile)) {
                $infoTranslation->description = $this->convertDescription(file_get_contents($languageFile));
            }

            if (is_file($languageFileMarkdown)) {
                $infoTranslation->description = $this->convertDescription($this->markdownParser->convertToHtml(file_get_contents($languageFileMarkdown))->getContent());
            }

            $infoTranslation->installationManual = '';

            if (is_file($languageManualFile)) {
                $infoTranslation->installationManual = $this->convertDescription(file_get_contents($languageManualFile));
            }

            if (is_file($languageManualFileMarkdown)) {
                $infoTranslation->installationManual = $this->convertDescription($this->markdownParser->convertToHtml(file_get_contents($languageManualFileMarkdown))->getContent());
            }

            if (is_file($languageHighlightsFile)) {
                $infoTranslation->highlights = file_get_contents($languageHighlightsFile);
            }

            if (is_file($languageFeaturesFile)) {
                $infoTranslation->features = file_get_contents($languageFeaturesFile);
            }

            if (file_exists($languageFaqFile)) {
                $infoTranslation->faqs = (new FaqReader())->parseFaq($languageFaqFile);
            }

            if ($language === 'de') {
                if ($plugin->getReader()->getDescriptionGerman()) {
                    $infoTranslation->shortDescription = mb_substr($plugin->getReader()->getDescriptionGerman(), 0, 185);
                }

                $infoTranslation->name = $plugin->getReader()->getLabelGerman();
            } else {
                if ($plugin->getReader()->getDescriptionEnglish()) {
                    $infoTranslation->shortDescription = mb_substr($plugin->getReader()->getDescriptionEnglish(), 0, 185);
                }

                $infoTranslation->name = $plugin->getReader()->getLabelEnglish();
            }
        }

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

        if (is_dir($imageDir)) {
            $images = $this->client->Plugins()->getImages($storePlugin->id);

            foreach ($images as $image) {
                $this->client->Plugins()->deleteImage($storePlugin->id, $image->id);
            }

            foreach (scandir($imageDir, SCANDIR_SORT_ASCENDING) as $image) {
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
                $plugin->license = License::make($licenseItem);
            }
        }
    }

    private function convertDescription(string $content): string
    {
        return strip_tags($content, '<a><b><i><em><strong><ul><ol><li><p><br><h2><h3><h4>');
    }
}
