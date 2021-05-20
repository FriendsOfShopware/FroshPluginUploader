<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\Generation\Shopware6;

use FroshPluginUploader\Components\Generation\ShopwarePlatform\ChangelogReader;
use FroshPluginUploader\Exception\MissingChangelogException;
use PHPUnit\Framework\TestCase;

class ChangelogReaderTest extends TestCase
{
    public function testNotFound(): void
    {
        static::expectException(MissingChangelogException::class);
        $reader = new ChangelogReader(__DIR__);
        $reader->getChangelog('de-DE', '1.0.0');
    }

    public function testChangelog(): void
    {
        $reader = new ChangelogReader(__DIR__ . '/test');
        static::assertSame('<ul><li>Test</li></ul>', $reader->getChangelog('en-GB', '1.0.0'));
        static::assertSame('<ul><li>Test</li></ul>', $reader->getChangelog('de-DE', '1.0.0'));

        static::assertSame('<ul><li>Test</li></ul>', $reader->getChangelog('de-DE', '1.0.1'));

        static::expectExceptionMessage('Cannot find changelog for version "1.0.2" with locale "de-DE"');
        static::assertSame('<ul><li>Test</li></ul>', $reader->getChangelog('de-DE', '1.0.2'));
    }

    public function testChangelogNotFoundLocale(): void
    {
        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('Changelog for locale "en-GB" does not exist');
        $reader = new ChangelogReader(__DIR__ . '/test2');
        static::assertSame('<ul><li>Test</li></ul>', $reader->getChangelog('en-GB', '1.0.0'));
    }
}
