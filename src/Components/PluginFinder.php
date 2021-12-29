<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin as Plugin5;
use FroshPluginUploader\Components\Generation\ShopwareApp\App;
use FroshPluginUploader\Components\Generation\ShopwarePlatform\Plugin as PluginPlatform;
use FroshPluginUploader\Exception\PluginGenerationException;
use const JSON_THROW_ON_ERROR;
use const SCANDIR_SORT_NONE;

class PluginFinder
{
    public static function findPluginByZipFile(string $unpackedFolder)
    {
        $dir = current(array_filter(scandir($unpackedFolder, SCANDIR_SORT_NONE), static function ($value) {
            return $value[0] !== '.';
        }));

        return self::findPluginByRootFolder($unpackedFolder . '/' . $dir);
    }

    public static function findPluginByRootFolder(string $rootFolder)
    {
        if (file_exists($rootFolder . '/plugin.xml')) {
            return new Plugin5($rootFolder, basename($rootFolder));
        }

        if (file_exists($rootFolder . '/manifest.xml')) {
            return new App($rootFolder, basename($rootFolder));
        }

        if (!is_file($pluginComposerJsonPath = $rootFolder . '/composer.json')) {
            throw new PluginGenerationException('Cannot detect plugin generation');
        }

        $data = json_decode(file_get_contents($pluginComposerJsonPath), true, 512, JSON_THROW_ON_ERROR);
        $type = $data['type'] ?? null;

        if ($type !== 'shopware-platform-plugin') {
            throw new PluginGenerationException('Cannot detect plugin generation by composer.json');
        }

        return new PluginPlatform($rootFolder, basename($rootFolder));
    }
}
