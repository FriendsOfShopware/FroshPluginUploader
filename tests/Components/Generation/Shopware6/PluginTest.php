<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\Generation\Shopware6;

use FroshPluginUploader\Components\Generation\ShopwarePlatform\Plugin;
use FroshPluginUploader\Components\StoreJsonLoader;
use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    public function testPlugin(): void
    {
        $rootDir = dirname(__DIR__, 3) . '/fixtures/plugins/ShopwarePlatformPlugin';
        $plugin = new Plugin($rootDir, 'ShopwarePlatformPlugin');

        static::assertSame('ShopwarePlatformPlugin', $plugin->getName());
        static::assertSame('ShopwarePlatformPlugin', $plugin->getReader()->getName());
        static::assertSame($rootDir, $plugin->getRootDir());
        static::assertSame('platform', $plugin->getStoreType());
        static::assertSame('1.0.0', $plugin->getReader()->getVersion());
        static::assertFalse($plugin->hasStoreJson());
        static::assertInstanceOf(StoreJsonLoader::class, $plugin->getStoreJson());
        static::assertIsArray($plugin->getReader()->all());
        static::assertSame('mit', $plugin->getReader()->getLicense());

        $versions = $plugin->getCompatibleVersions([
            [
                'selectable' => false,
                'major' => 'Shopware 6',
                'name' => '5.2'
            ],
            [
                'selectable' => true,
                'major' => 'Shopware 5',
                'name' => '5.2'
            ],
            [
                'selectable' => true,
                'major' => 'Shopware 6',
                'name' => '6.1.0-ea1'
            ],
            [
                'selectable' => true,
                'major' => 'Shopware 6',
                'name' => '6.1.0'
            ]
        ]);

        static::assertSame('6.1.0', $versions[0]['name']);
        static::assertSame('Label EN', $plugin->getReader()->getLabelEnglish());
        static::assertSame('Label DE', $plugin->getReader()->getLabelGerman());
        static::assertSame('Description EN', $plugin->getReader()->getDescriptionEnglish());
        static::assertSame('Description DE', $plugin->getReader()->getDescriptionGerman());
        static::assertSame('<ul><li>First release in store</li></ul>', $plugin->getReader()->getNewestChangelogEnglish());
        static::assertSame('<ul><li>Erster Release im Store</li></ul>', $plugin->getReader()->getNewestChangelogGerman());
    }

    public function testMultiLicense(): void
    {
        $rootDir = dirname(__DIR__, 3) . '/fixtures/plugins/ShopwarePlatformPluginComposerMultiLicense';
        $plugin = new Plugin($rootDir, 'ShopwarePlatformPluginComposerMultiLicense');

        static::assertSame('mit', $plugin->getReader()->getLicense());
    }
}
