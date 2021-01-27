<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\ShopwareApp;

use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Components\PluginReaderInterface;
use FroshPluginUploader\Components\StoreJsonLoader;

class App implements PluginInterface
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $pluginName;

    public function __construct(string $rootFolder, string $pluginName)
    {
        $this->rootDir = $rootFolder;
        $this->pluginName = $pluginName;
    }

    public function getName(): string
    {
        return $this->pluginName;
    }

    public function getReader(): PluginReaderInterface
    {
        return new AppReader($this->rootDir);
    }

    public function hasStoreJson(): bool
    {
        return file_exists($this->getResourcesFolderPath() . 'store.json');
    }

    public function getStoreJson(): StoreJsonLoader
    {
        return new StoreJsonLoader($this->getResourcesFolderPath() . 'store.json');
    }

    public function getResourcesFolderPath(): string
    {
        return $this->rootDir . '/store/';
    }

    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    public function getCompatibleVersions(array $versions): array
    {
        return [];
    }

    public function getStoreType(): string
    {
        return 'apps';
    }
}
