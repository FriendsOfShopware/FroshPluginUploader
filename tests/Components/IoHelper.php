<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components;

use Symfony\Component\Filesystem\Filesystem;

class IoHelper
{
    private static $registered = false;

    private static $deleteFolderList = [];

    /**
     * @var array<string, \ZipArchive>
     */
    private static $deleteZipList = [];

    public static function makeZip(): \ZipArchive
    {
        static::registerCleanup();

        $zip = new \ZipArchive();
        $path = sys_get_temp_dir() . '/' . uniqid(__METHOD__, true) . '.zip';
        $zip->open($path, \ZipArchive::CREATE);

        static::$deleteZipList[$path] = $zip;

        return $zip;
    }

    public static function makeFolder(): string
    {
        static::registerCleanup();

        $path = sys_get_temp_dir() . '/' . uniqid(__FUNCTION__, true);
        static::$deleteFolderList[] = $path;
        (new Filesystem())->mkdir($path);

        return $path;
    }

    private static function registerCleanup(): void
    {
        if (static::$registered) {
            return;
        }

        register_shutdown_function([self::class, 'cleanup']);
        static::$registered = true;
    }

    public static function cleanup(): void
    {
        $fs = new Filesystem();

        foreach (static::$deleteFolderList as $item) {
            $fs->remove($item);
        }

        foreach (static::$deleteZipList as $path => $zipArchive) {
            $zipArchive->close();
            $fs->remove($path);
        }
    }
}