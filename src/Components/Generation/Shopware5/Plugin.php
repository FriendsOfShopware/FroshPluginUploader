<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\Shopware5;

use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Components\PluginReaderInterface;

class Plugin implements PluginInterface
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
        return new PluginReader($this->rootDir);
    }

    public function getResourcesFolderPath(): string
    {
        return $this->rootDir . '/Resources/store/';
    }

    public function getRootDir(): string
    {
        return $this->rootDir;
    }
}
