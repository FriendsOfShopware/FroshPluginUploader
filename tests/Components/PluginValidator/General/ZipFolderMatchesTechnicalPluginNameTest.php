<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\PluginValidator\General;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin;
use FroshPluginUploader\Components\PluginValidator\General\ZipFolderMatchesTechnicalPluginName;
use FroshPluginUploader\Structs\ViolationContext;
use PHPUnit\Framework\TestCase;

class ZipFolderMatchesTechnicalPluginNameTest extends TestCase
{
    public function testInvalidNaming(): void
    {
        $context = $this->createContextWithError('test', 'foo');
        (new ZipFolderMatchesTechnicalPluginName())->validate($context);

        static::assertTrue($context->hasViolations());
        static::assertContains('The plugin needs to be zipped with an folder "foo". You can use unzip -l Plugin.zip to show the content', $context->getViolations());
    }

    public function testValidNaming(): void
    {
        $context = $this->createContextWithError('test', 'test');
        (new ZipFolderMatchesTechnicalPluginName())->validate($context);

        static::assertFalse($context->hasViolations());
    }

    private function createContextWithError(string $zipName, string $pluginName): ViolationContext
    {
        $zip = new \ZipArchive();
        $zip->open(sys_get_temp_dir() . '/' . uniqid(__METHOD__, true) . '.zip', \ZipArchive::CREATE);
        $zip->addEmptyDir($zipName);

        $plugin = $this->createMock(Plugin::class);
        $plugin->method('getName')->willReturn($pluginName);

        return new ViolationContext($plugin, $zip, __DIR__);
    }
}