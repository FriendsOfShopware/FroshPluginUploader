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
        $pluginInformation = json_decode((string) $this->client->get(sprintf('/plugins/%d', Util::getEnv('PLUGIN_ID')))->getBody(), true);

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
        }

        unset($infoTranslation);

        $this->client->put(sprintf('/plugins/%d', Util::getEnv('PLUGIN_ID')), ['json' => $pluginInformation]);

        $imageDir = $folder . '/images';

        if (file_exists($imageDir)) {
            $images = json_decode((string) $this->client->get(sprintf('/plugins/%d/pictures', Util::getEnv('PLUGIN_ID')))->getBody(), true);

            foreach ($images as $image) {
                $this->client->delete(sprintf('/plugins/%d/pictures/%d', Util::getEnv('PLUGIN_ID'), $image['id']));
            }

            foreach (scandir($imageDir, SCANDIR_SORT_ASCENDING) as $image) {
                if ($image[0] === '.') {
                    continue;
                }

                $this->client->post(sprintf('/plugins/%d/pictures', Util::getEnv('PLUGIN_ID')), [
                    'multipart' => [
                        [
                            'name' => 'file',
                            'contents' => fopen($imageDir . '/' . $image, 'rb'),
                        ],
                    ],
                ]);
            }
        }
    }
}
