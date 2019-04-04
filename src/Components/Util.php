<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Structs\Config\PluginConfig;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class Util
{
    public static $configDirectory = "config/plugins/";

    /**
     * @param      $name
     * @param bool $default
     *
     * @return array|bool|false|string
     */
    public static function getEnv($name, $default = false)
    {
        $var = getenv($name);

        if (!$var) {
            $var = $default;
        }

        return $var;
    }

    /**
     * @param string|null $prefix
     *
     * @return string
     * @throws \Exception
     */
    public static function mkTempDir(?string $prefix = null): string
    {
        if ($prefix === null) {
            $prefix = (string)random_int(PHP_INT_MIN, PHP_INT_MAX);
        }

        $tmpFolder = sys_get_temp_dir() . '/' . uniqid($prefix, true);

        if (!mkdir($tmpFolder) && !is_dir($tmpFolder)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $tmpFolder));
        }

        return $tmpFolder;
    }

    /**
     * @param string $tmpFolder
     *
     * @return mixed
     */
    public static function getPluginName(string $tmpFolder)
    {
        return current(
            array_filter(
                scandir($tmpFolder, SCANDIR_SORT_NONE),
                function ($value) {
                    return $value[0] !== '.';
                }
            )
        );
    }

    /**
     * If hasPath is false, all Plugin-configs will be gathered
     * @param bool $hasPath
     *
     * @return array
     */
    public static function getPluginConfigs($hasPath = false)
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir(self::$configDirectory);

        $finder = new Finder();
        $finder->files()->in(self::$configDirectory);

        $plugins = [];

        /**
         * @var PluginConfig $config ;
         */
        foreach ($finder as $file) {
            $config = (object)Yaml::parseFile(self::$configDirectory . $file->getFilename());
            if ($hasPath) {
                if (!empty($config->path)) {
                    $plugins[$config->id] = $config;
                }
            } else {
                $plugins[$config->id] = $config;
            }
        }

        return $plugins;
    }
}
