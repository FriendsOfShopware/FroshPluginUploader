<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\ZipStrategy;

use FroshPluginUploader\Components\ZipStrategy\PlainStrategy;
use FroshPluginUploader\Tests\Components\IoHelper;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PlainStrategyTest extends TestCase
{
    public function testFolderWithoutGit(): void
    {
        $src = IoHelper::makeFolder();
        $target = IoHelper::makeFolder();

        touch($src . '/plugin.png');

        $plainStrategy = new PlainStrategy();
        $plainStrategy->copyFolder($src, $target);

        static::assertFileExists($target . '/plugin.png');
    }
}
