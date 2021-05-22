<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin as Plugin5;
use FroshPluginUploader\Components\Generation\Shopware5\PluginReader as PluginReader5;
use FroshPluginUploader\Components\Generation\ShopwareApp\App;
use FroshPluginUploader\Components\Generation\ShopwareApp\AppReader;
use FroshPluginUploader\Components\Generation\ShopwarePlatform\Plugin as PluginPlatform;
use FroshPluginUploader\Components\Generation\ShopwarePlatform\PluginReader as PluginReaderPlatform;
use FroshPluginUploader\Components\PluginFinder;
use FroshPluginUploader\Components\PluginReaderInterface;
use FroshPluginUploader\Exception\PluginGenerationException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PluginFinderTest extends TestCase
{
    public function testShopware5(): void
    {
        $plugin = PluginFinder::findPluginByRootFolder(__DIR__ . '/../fixtures/plugins/Shopware5Plugin');

        self::assertInstanceOf(Plugin5::class, $plugin);
        self::assertInstanceOf(PluginReader5::class, $plugin->getReader());
        self::assertInstanceOf(PluginReaderInterface::class, $plugin->getReader());
    }

    public function testShopwarePlatform(): void
    {
        $plugin = PluginFinder::findPluginByRootFolder(__DIR__ . '/../fixtures/plugins/ShopwarePlatformPlugin');

        self::assertInstanceOf(PluginPlatform::class, $plugin);
        self::assertInstanceOf(PluginReaderPlatform::class, $plugin->getReader());
        self::assertInstanceOf(PluginReaderInterface::class, $plugin->getReader());
    }

    public function testShopwareApp(): void
    {
        $plugin = PluginFinder::findPluginByRootFolder(__DIR__ . '/../fixtures/plugins/ShopwareApp');

        self::assertInstanceOf(App::class, $plugin);
        self::assertInstanceOf(AppReader::class, $plugin->getReader());
        self::assertInstanceOf(PluginReaderInterface::class, $plugin->getReader());
    }

    public function testShopwareAppZip(): void
    {
        $zip = new \ZipArchive();
        $zip->open(__DIR__ . '/../fixtures/plugins/ShopwareApp.zip');
        $tmpFolder = sys_get_temp_dir() . '/' . uniqid(__FUNCTION__, true);
        mkdir($tmpFolder);
        $zip->extractTo($tmpFolder);

        $plugin = PluginFinder::findPluginByZipFile($tmpFolder);

        self::assertInstanceOf(App::class, $plugin);
        self::assertInstanceOf(AppReader::class, $plugin->getReader());
        self::assertInstanceOf(PluginReaderInterface::class, $plugin->getReader());
    }

    public function testShopwareInvalidComposerJson(): void
    {
        self::expectException(PluginGenerationException::class);
        self::expectExceptionMessage('Cannot detect plugin generation by composer.json');

        PluginFinder::findPluginByRootFolder(__DIR__ . '/../fixtures/plugins/ShopwareInvalidComposerJson');
    }

    public function testInvalidFolder(): void
    {
        self::expectException(PluginGenerationException::class);
        self::expectExceptionMessage('Cannot detect plugin generation');

        PluginFinder::findPluginByRootFolder(sys_get_temp_dir());
    }
}
