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
        $composerJson = json_decode(file_get_contents($rootFolder . '/composer.json'), true)['extra'];
        $className = explode('\\', $composerJson['shopware-plugin-class']);

        $this->pluginName = end($className);
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

    public function getCompatibleMajorVersion(): string
    {
        return 'Shopware 6';
    }
}
