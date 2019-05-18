<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\ShopwarePlatform;

use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Components\PluginReaderInterface;
use FroshPluginUploader\Components\StoreJsonLoader;

class Plugin implements PluginInterface
{
    /**
     * @var string
     */
    private $rootFolder;
    /**
     * @var string
     */
    private $pluginName;

    public function __construct(string $rootFolder, string $pluginName)
    {
        $this->rootFolder = $rootFolder;
        $this->pluginName = $pluginName;
    }

    public function getName(): string
    {
        return $this->pluginName;
    }

    public function getReader(): PluginReaderInterface
    {
        return new PluginReader($this->rootFolder);
    }

    public function getStoreJson(): StoreJsonLoader
    {
        return new StoreJsonLoader($this->getResourcesFolderPath() . 'store.json');
    }

    public function getResourcesFolderPath(): string
    {
        return $this->rootFolder . '/src/Resources/store/';
    }

    public function getRootDir(): string
    {
        return $this->rootFolder;
    }
}
