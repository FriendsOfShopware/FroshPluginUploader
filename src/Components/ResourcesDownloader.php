<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Structs\Plugin;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use RuntimeException;

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

    public function download(string $pluginName, string $path): void
    {
        // @codeCoverageIgnoreStart
        if (!is_dir($path) && !mkdir($path) && !is_dir($path)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
        }

        $imagesPath = $path . '/images/';
        if (!is_dir($imagesPath) && !mkdir($imagesPath) && !is_dir($imagesPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $imagesPath));
        }
        // @codeCoverageIgnoreEnd

        $plugin = $this->client->Producer()->getPlugin($pluginName);

        foreach ($plugin->infos as $info) {
            $locale = mb_substr($info->locale->name, 0, 2);

            if (!empty($info->description)) {
                file_put_contents($path . '/' . $locale . '.html', $info->description);
            }

            if (!empty($info->installationManual)) {
                file_put_contents($path . '/' . $locale . '_manual.html', $info->installationManual);
            }
        }

        file_put_contents($path . '/store.json', json_encode($this->generateStoreJson($plugin), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        $pictures = $this->client->Plugins()->getImages($plugin->id);

        $i = 0;
        foreach ($pictures as $picture) {
            copy($picture->remoteLink, $imagesPath . '/' . $i . '.png');
            $i++;
        }
    }

    private function generateStoreJson(Plugin $plugin): array
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
                $json['tags'][mb_substr($info->locale->name, 0, 2)] = array_map([$this, 'getNames'], $info->tags);
            }
        }

        foreach ($plugin->infos as $info) {
            if (!empty($info->videos)) {
                $json['videos'][mb_substr($info->locale->name, 0, 2)] = array_map([$this, 'getUrls'], $info->videos);
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
