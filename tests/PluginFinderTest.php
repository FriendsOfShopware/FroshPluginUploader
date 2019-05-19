<?php


namespace FroshPluginUploader\Tests;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin as Plugin5;
use FroshPluginUploader\Components\Generation\Shopware5\PluginReader as PluginReader5;
use FroshPluginUploader\Components\Generation\ShopwarePlatform\Plugin as PluginPlatform;
use FroshPluginUploader\Components\Generation\ShopwarePlatform\PluginReader as PluginReaderPlatform;
use FroshPluginUploader\Components\PluginFinder;
use FroshPluginUploader\Components\PluginReaderInterface;
use FroshPluginUploader\Exception\PluginGenerationException;
use PHPUnit\Framework\TestCase;

class PluginFinderTest extends TestCase
{
    public function testShopware5(): void
    {
        $plugin = PluginFinder::findPluginByRootFolder(__DIR__ . '/testPlugins/Shopware5Plugin');

        $this->assertInstanceOf(Plugin5::class, $plugin);
        $this->assertInstanceOf(PluginReader5::class, $plugin->getReader());
        $this->assertInstanceOf(PluginReaderInterface::class, $plugin->getReader());
    }

    public function testShopwarePlatform(): void
    {
        $plugin = PluginFinder::findPluginByRootFolder(__DIR__ . '/testPlugins/ShopwarePlatformPlugin');

        $this->assertInstanceOf(PluginPlatform::class, $plugin);
        $this->assertInstanceOf(PluginReaderPlatform::class, $plugin->getReader());
        $this->assertInstanceOf(PluginReaderInterface::class, $plugin->getReader());
    }

    public function testShopwareInvalidComposerJson(): void
    {
        $this->expectException(PluginGenerationException::class);
        $this->expectExceptionMessage('Cannot detect plugin generation by composer.json');

        PluginFinder::findPluginByRootFolder(__DIR__ . '/testPlugins/ShopwareInvalidComposerJson');
    }

    public function testInvalidFolder(): void
    {
        $this->expectException(PluginGenerationException::class);
        $this->expectExceptionMessage('Cannot detect plugin generation');

        PluginFinder::findPluginByRootFolder(sys_get_temp_dir());
    }
}