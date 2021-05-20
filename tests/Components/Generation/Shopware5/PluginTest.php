<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\Generation\Shopware5;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin;
use FroshPluginUploader\Components\StoreJsonLoader;
use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    public function testCreate(): void
    {
        $rootDir = dirname(__DIR__, 3) . '/fixtures/plugins/Shopware5Plugin';
        $plugin = new Plugin($rootDir, 'Shopware5Plugin');
        static::assertSame('Shopware5Plugin', $plugin->getName());
        static::assertSame('Shopware5Plugin', $plugin->getReader()->getName());
        static::assertSame($rootDir, $plugin->getRootDir());
        static::assertFalse($plugin->hasStoreJson());
        static::assertInstanceOf(StoreJsonLoader::class, $plugin->getStoreJson());
        static::assertIsArray($plugin->getReader()->all());
        static::assertSame('1.0.0', $plugin->getReader()->getVersion());
        static::assertSame('mit', $plugin->getReader()->getLicense());
        static::assertSame('classic', $plugin->getStoreType());

        $versions = $plugin->getCompatibleVersions([
            [
                'selectable' => false,
                'major' => 'Shopware 5',
                'name' => '5.2'
            ],
            [
                'selectable' => true,
                'major' => 'Shopware 6',
                'name' => '5.2'
            ],
            [
                'selectable' => true,
                'major' => 'Shopware 5',
                'name' => '5.4.0'
            ]
        ]);

        static::assertSame('5.4.0', $versions[0]['name']);
        static::assertSame('5.4.0', $plugin->getReader()->getMinVersion());
        static::assertSame('5.6.0', $plugin->getReader()->getMaxVersion());
        static::assertSame('Label DE', $plugin->getReader()->getLabelGerman());
        static::assertSame('Label EN', $plugin->getReader()->getLabelEnglish());
        static::assertSame('Changelog EN', $plugin->getReader()->getNewestChangelogEnglish());
        static::assertSame('Changelog DE', $plugin->getReader()->getNewestChangelogGerman());
        static::assertSame('Description EN', $plugin->getReader()->getDescriptionEnglish());
        static::assertSame('Description DE', $plugin->getReader()->getDescriptionGerman());
    }

    public function testCreateWithDefaultCompability(): void
    {
        $rootDir = dirname(__DIR__, 3) . '/fixtures/plugins/Shopware5PluginWithoutCompability';
        $plugin = new Plugin($rootDir, 'Shopware5PluginWithoutCompability');
        static::assertSame('Shopware5PluginWithoutCompability', $plugin->getName());
        static::assertSame('Shopware5PluginWithoutCompability', $plugin->getReader()->getName());
        static::assertSame($rootDir, $plugin->getRootDir());
        static::assertFalse($plugin->hasStoreJson());
        static::assertInstanceOf(StoreJsonLoader::class, $plugin->getStoreJson());
        static::assertIsArray($plugin->getReader()->all());
        static::assertSame('1.0.0', $plugin->getReader()->getVersion());
        static::assertSame('mit', $plugin->getReader()->getLicense());
        static::assertSame('classic', $plugin->getStoreType());

        $versions = $plugin->getCompatibleVersions([
            [
                'selectable' => false,
                'major' => 'Shopware 5',
                'name' => '5.2'
            ],
            [
                'selectable' => true,
                'major' => 'Shopware 6',
                'name' => '5.2'
            ],
            [
                'selectable' => true,
                'major' => 'Shopware 5',
                'name' => '5.2.0'
            ]
        ]);

        static::assertSame('5.2.0', $versions[0]['name']);
        static::assertSame('5.2.0', $plugin->getReader()->getMinVersion());
        static::assertSame(null, $plugin->getReader()->getMaxVersion());
        static::assertSame('Label DE', $plugin->getReader()->getLabelGerman());
        static::assertSame('Label EN', $plugin->getReader()->getLabelEnglish());
        static::assertSame('Changelog EN', $plugin->getReader()->getNewestChangelogEnglish());
        static::assertSame('Changelog DE', $plugin->getReader()->getNewestChangelogGerman());
        static::assertSame('Description EN', $plugin->getReader()->getDescriptionEnglish());
        static::assertSame('Description DE', $plugin->getReader()->getDescriptionGerman());
    }
}
