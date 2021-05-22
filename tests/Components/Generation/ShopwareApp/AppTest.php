<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\Generation\ShopwareApp;

use FroshPluginUploader\Components\Generation\ShopwareApp\App;
use FroshPluginUploader\Components\StoreJsonLoader;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class AppTest extends TestCase
{
    public function testReading(): void
    {
        $rootDir = dirname(__DIR__, 3) . '/fixtures/plugins/ShopwareApp';
        $app = new App($rootDir, 'FroshGoogleSheet');
        static::assertSame('FroshGoogleSheet', $app->getName());
        static::assertSame('apps', $app->getStoreType());
        static::assertSame($rootDir, $app->getRootDir());
        static::assertFalse($app->hasStoreJson());
        static::assertIsArray($app->getCompatibleVersions([]));
        static::assertEmpty($app->getCompatibleVersions([]));
        static::assertInstanceOf(StoreJsonLoader::class, $app->getStoreJson());

        static::assertIsArray($app->getReader()->all());
        static::assertSame('1.0.0', $app->getReader()->getVersion());
        static::assertSame('mit', $app->getReader()->getLicense());
        static::assertSame('', $app->getReader()->getDescriptionGerman());
        static::assertSame('', $app->getReader()->getDescriptionEnglish());
        static::assertSame($app->getName(), $app->getReader()->getName());
        static::assertSame('Google Sheet Synchronization', $app->getReader()->getLabelGerman());
        static::assertSame('Google Sheet Synchronization', $app->getReader()->getLabelEnglish());
        static::assertSame('<ul><li>Erster Release im Store</li></ul>', $app->getReader()->getNewestChangelogGerman());
        static::assertSame('<ul><li>First release in store</li></ul>', $app->getReader()->getNewestChangelogEnglish());
    }
}
