<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;

class ResourcesDownloader
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function download(string $path): void
    {
        $pluginId = (int) Util::getEnv('PLUGIN_ID');

        if (!file_exists($path)) {
            if (!mkdir($path) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }

        $imagesPath = $path . '/images/';
        if (!file_exists($imagesPath)) {
            if (!mkdir($imagesPath) && !is_dir($imagesPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $imagesPath));
            }
        }

        $plugin = $this->client->Plugins()->get($pluginId);

        foreach ($plugin->infos as $info) {
            $locale = substr($info->locale->name, 0, 2);

            if (!empty($info->description)) {
                file_put_contents($path . '/' . $locale . '.html', $info->description);
            }

            if (!empty($info->installationManual)) {
                file_put_contents($path . '/' . $locale . '_manual.html', $info->installationManual);
            }
        }

        $pictures = $this->client->Plugins()->getImages($pluginId);

        $i = 0;
        foreach ($pictures as $picture) {
            copy($picture->remoteLink, $imagesPath . '/' . $i . '.png');
            ++$i;
        }
    }
}
