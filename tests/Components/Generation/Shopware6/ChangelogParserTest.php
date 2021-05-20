<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\Generation\Shopware6;

use FroshPluginUploader\Components\Generation\ShopwarePlatform\ChangelogParser;
use FroshPluginUploader\Exception\ChangelogInvalidException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * @internal
 * @coversNothing
 */
class ChangelogParserTest extends TestCase
{
    public function testNotExists(): void
    {
        static::expectException(FileNotFoundException::class);
        $changelog = new ChangelogParser();
        $changelog->parseChangelog('foo.md');
    }

    public function testInvalidFile(): void
    {
        static::expectException(ChangelogInvalidException::class);
        $changelog = new ChangelogParser();
        $changelog->parseChangelog(__DIR__ . '/invalid.md');
    }
}
