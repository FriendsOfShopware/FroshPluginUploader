<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\ZipStrategy;

use FroshPluginUploader\Components\ZipStrategy\GitStrategy;
use FroshPluginUploader\Tests\Components\IoHelper;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class GitStrategyTest extends TestCase
{
    public function testFolderWithGit(): void
    {
        [$src, $target] = $this->prepare();

        $gitStrategy = new GitStrategy(null);
        $gitStrategy->copyFolder($src, $target);

        static::assertFileExists($target . '/plugin.png');
        static::assertFileDoesNotExist($target . '/untracked_file.png');
    }

    public function testInvalidBranch(): void
    {
        static::expectException(\RuntimeException::class);
        [$src, $target] = $this->prepare();

        $gitStrategy = new GitStrategy('next');
        $gitStrategy->copyFolder($src, $target);
    }

    private function prepare(): array
    {
        $src = IoHelper::makeFolder();
        $target = IoHelper::makeFolder();

        touch($src . '/plugin.png');

        exec('cd ' . $src . '; git init; git add .');
        exec('cd ' . $src . '; git config user.name Test');
        exec('cd ' . $src . '; git config user.email test@test.de');
        exec('cd ' . $src . '; git commit -m "Test"');

        touch($src . '/untracked_file.png');

        return [$src, $target];
    }
}
