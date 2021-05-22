<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\PluginValidator\General;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin;
use FroshPluginUploader\Components\PluginValidator\General\NotAllowedFilesInZipChecker;
use FroshPluginUploader\Structs\ViolationContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class NotAllowedFilesInZipCheckerTest extends TestCase
{
    public function testChecker(): void
    {
        $context = $this->createContextWithAllErrors();

        static::assertTrue((new NotAllowedFilesInZipChecker())->supports($context));

        (new NotAllowedFilesInZipChecker())->validate($context);
        static::assertTrue($context->hasViolations());

        static::assertContains('Directory traversal detected', $context->getViolations());
        static::assertContains('Found vcs repository inside zip.', $context->getViolations());
        static::assertContains('Not allowed file or folder test.zip detected. Please remove it', $context->getViolations());
        static::assertContains('Not allowed file or folder test.tar detected. Please remove it', $context->getViolations());
        static::assertContains('Not allowed file or folder test.tar.gz detected. Please remove it', $context->getViolations());
        static::assertContains('Not allowed file or folder test.phar detected. Please remove it', $context->getViolations());
        static::assertContains('Not allowed file or folder .DS_Store detected. Please remove it', $context->getViolations());
        static::assertContains('Not allowed file or folder Thumbs.db detected. Please remove it', $context->getViolations());
    }

    private function createContextWithAllErrors(): ViolationContext
    {
        $zip = new \ZipArchive();
        $zip->open(sys_get_temp_dir() . '/' . uniqid(__METHOD__, true) . '.zip', \ZipArchive::CREATE);
        $zip->addFromString('../test.txt', 'Huhu');
        $zip->addFromString('.git/.gitconfig', 'Huhu');
        $zip->addFromString('test.zip', 'Huhu');
        $zip->addFromString('test.tar', 'Huhu');
        $zip->addFromString('test.tar.gz', 'Huhu');
        $zip->addFromString('test.phar', 'Huhu');
        $zip->addFromString('.DS_Store', 'Huhu');
        $zip->addFromString('Thumbs.db', 'Huhu');
        $zip->addFromString('.phar', 'Huhu');

        return new ViolationContext($this->createMock(Plugin::class), $zip, __DIR__);
    }
}
