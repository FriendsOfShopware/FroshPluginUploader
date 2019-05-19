<?php

namespace FroshPluginUploader\Tests;

use FroshPluginUploader\Components\Generation\Shopware5\PluginReader;
use PHPUnit\Framework\TestCase;

class Shopware5PluginReaderTest extends TestCase
{
    public function testReading(): void
    {
        $pluginReader = new PluginReader(__DIR__ . '/testPlugins/Shopware5Plugin');

        $this->assertEquals('Label DE', $pluginReader->getLabelGerman());
        $this->assertEquals('Label EN', $pluginReader->getLabelEnglish());
        $this->assertEquals('Description EN', $pluginReader->getDescriptionEnglish());
        $this->assertEquals('Description DE', $pluginReader->getDescriptionGerman());
        $this->assertEquals('1.0.0', $pluginReader->getVersion());
        $this->assertEquals('5.4.0', $pluginReader->getMinVersion());
        $this->assertNull($pluginReader->getMaxVersion());
        $this->assertEquals('Changelog EN', $pluginReader->getNewestChangelogEnglish());
        $this->assertEquals('Changelog DE', $pluginReader->getNewestChangelogGerman());

        $pluginReader->validate();
    }

    public function testInvalidDescription(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Description is not defined in plugin.xml');

        $pluginReader = new PluginReader(__DIR__ . '/testPlugins/Shopware5PluginInvalidDescription');

        $pluginReader->validate();
    }

    public function testMissingChangelogTranslation(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Changelog for version 1.0.0 has no translation for en');

        $pluginReader = new PluginReader(__DIR__ . '/testPlugins/Shopware5PluginMissingChangelog');

        $pluginReader->validate();
    }
}