<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;

class PluginUpdater
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
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

        foreach ($pluginInformation['infos'] as &$infoTranslation) {
            $language = substr($infoTranslation['locale']['name'], 0, 2);
            $languageFile = $folder . '/' . $language . '.html';
            $languageManualFile = $folder . '/' . $language . '_manual.html';

            if (file_exists($languageFile)) {
                $infoTranslation['description'] = file_get_contents($languageFile);
            }

            if (file_exists($languageManualFile)) {
                $infoTranslation['installationManual'] = file_get_contents($languageManualFile);
            } else {
                $infoTranslation['installationManual'] = '';
            }

            if ($plugin) {
                if ($language === 'de') {
                    $infoTranslation['shortDescription'] = $plugin->getDescriptionGerman();
                    $infoTranslation['name'] = $plugin->getLabelGerman();
                } else {
                    $infoTranslation['shortDescription'] = $plugin->getDescriptionEnglish();
                    $infoTranslation['name'] = $plugin->getLabelEnglish();
                }
            }
        }

        unset($infoTranslation);

        $this->client->Plugins()->put($pluginId, $pluginInformation);

        $imageDir = $folder . '/images';

        if (file_exists($imageDir)) {
            $images = $this->client->Plugins()->getImages($pluginId);

            foreach ($images as $image) {
                $this->client->Plugins()->deleteImage($pluginId, $image['id']);
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
