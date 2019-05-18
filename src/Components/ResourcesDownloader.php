<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Structs\Plugin;

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

        file_put_contents($path . '/store.json', json_encode($this->generateStoreJson($plugin), JSON_PRETTY_PRINT));

        $pictures = $this->client->Plugins()->getImages($pluginId);

        $i = 0;
        foreach ($pictures as $picture) {
            copy($picture->remoteLink, $imagesPath . '/' . $i . '.png');
            ++$i;
        }
    }

    private function generateStoreJson(Plugin $plugin)
    {
        $json = [
            'storeAvailabilities' => array_map([$this, 'getNames'], $plugin->storeAvailabilities),
            'localizations' => array_map([$this, 'getNames'], $plugin->localizations),
            'categories' => array_map([$this, 'getNames'], $plugin->categories),
            'productType' => $plugin->productType->name,
            'responsive' => $plugin->responsive,
            'standardLocale' => $plugin->standardLocale->name,
            'tags' => [],
            'videos' => [],
        ];

        foreach ($plugin->infos as $info) {
            if (!empty($info->tags)) {
                $json['tags'][substr($info->locale->name, 0, 2)] = array_map([$this, 'getNames'], $info->tags);
            }
        }

        foreach ($plugin->infos as $info) {
            if (!empty($info->videos)) {
                $json['videos'][substr($info->locale->name, 0, 2)] = array_map([$this, 'getUrls'], $info->videos);
            }
        }

        return $json;
    }

    private function getNames($someStruct)
    {
        return $someStruct->name;
    }

    private function getUrls($someStruct)
    {
        return $someStruct->url;
    }
}
