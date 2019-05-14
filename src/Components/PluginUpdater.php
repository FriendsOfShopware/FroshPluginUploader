<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;

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

    public function sync(string $folder): void
    {
        $pluginId = (int) Util::getEnv('PLUGIN_ID');

        $pluginInformation = $this->client->Plugins()->get($pluginId);
        $pluginFolder = dirname($folder, 2);
        $plugin = null;

        if (file_exists($pluginFolder . '/plugin.xml')) {
            $plugin = new PluginReader($pluginFolder);
        }

        foreach ($pluginInformation->infos as &$infoTranslation) {
            $language = substr($infoTranslation->locale->name, 0, 2);
            $languageFile = $folder . '/' . $language . '.html';
            $languageFileMarkdown = $folder . '/' . $language . '.md';
            $languageManualFile = $folder . '/' . $language . '_manual.html';
            $languageManualFileMarkdown = $folder . '/' . $language . '_manual.md';
            $languageHighlightsFile = $folder . '/' . $language . '_highlights.txt';
            $languageFeaturesFile = $folder . '/' . $language . '_features.txt';

            if (file_exists($languageFile)) {
                $infoTranslation->description = file_get_contents($languageFile);
            }

            if (file_exists($languageFileMarkdown)) {
                $infoTranslation->description = $this->markdownParser->parse(file_get_contents($languageFileMarkdown));
            }

            $infoTranslation->installationManual = '';

            if (file_exists($languageManualFile)) {
                $infoTranslation->installationManual = file_get_contents($languageManualFile);
            }

            if (file_exists($languageManualFileMarkdown)) {
                $infoTranslation->installationManual = $this->markdownParser->parse(file_get_contents($languageManualFileMarkdown));
            }

            if (file_exists($languageHighlightsFile)) {
                $infoTranslation->highlights = file_get_contents($languageHighlightsFile);
            }

            if (file_exists($languageFeaturesFile)) {
                $infoTranslation->features = file_get_contents($languageFeaturesFile);
            }

            if ($plugin) {
                if ($language === 'de') {
                    $infoTranslation->shortDescription = $plugin->getDescriptionGerman();
                    $infoTranslation->name = $plugin->getLabelGerman();
                } else {
                    $infoTranslation->shortDescription = $plugin->getDescriptionEnglish();
                    $infoTranslation->name = $plugin->getLabelEnglish();
                }
            }
        }

        unset($infoTranslation);

        $this->client->Plugins()->put($pluginId, $pluginInformation);

        $imageDir = $folder . '/images';

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
}
