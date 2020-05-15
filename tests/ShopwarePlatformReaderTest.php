<?php


namespace FroshPluginUploader\Tests;


use FroshPluginUploader\Components\Generation\ShopwarePlatform\PluginReader;
use PHPUnit\Framework\TestCase;

class ShopwarePlatformReaderTest extends TestCase
{
    public function testReading(): void
    {
        $pluginReader = new PluginReader(__DIR__ . '/testPlugins/ShopwarePlatformPlugin');

        $this->assertEquals('Label DE', $pluginReader->getLabelGerman());
        $this->assertEquals('Label EN', $pluginReader->getLabelEnglish());
        $this->assertEquals('Description EN', $pluginReader->getDescriptionEnglish());
        $this->assertEquals('Description DE', $pluginReader->getDescriptionGerman());
        $this->assertEquals('1.0.0', $pluginReader->getVersion());
        $this->assertEquals('6.0.0', $pluginReader->getMinVersion());
        $this->assertNull($pluginReader->getMaxVersion());
        $this->assertEquals('<ul><li>First release in store</li></ul>', $pluginReader->getNewestChangelogEnglish());
        $this->assertEquals('<ul><li>Erster Release im Store</li></ul>', $pluginReader->getNewestChangelogGerman());

        $pluginReader->validate();
    }

    public function testInvalidComposerType(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Type is not defined in composer.json');

        $pluginReader = new PluginReader(__DIR__ . '/testPlugins/ShopwareInvalidComposerJson');

        $pluginReader->validate();
    }

    public function testMissingChangelog(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Changelogs are missing for plugin');

        $pluginReader = new PluginReader(__DIR__ . '/testPlugins/ShopwareInvalidComposerJson');
        $pluginReader->getNewestChangelogGerman();
    }

    public function testPluginIsWrappedProperly(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The folder is not wrapped inside a folder having the plugins name Performance but shopware-platform-plugin-in-a-folder-not-matching-the-name-of-its-content instead');

        $pluginReader = new PluginReader(__DIR__ . '/testPlugins/shopware-platform-plugin-in-a-folder-not-matching-the-name-of-its-content');
        $pluginReader->validate();
    }
}