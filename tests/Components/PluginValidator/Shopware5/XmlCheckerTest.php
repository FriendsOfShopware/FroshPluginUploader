<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\PluginValidator\Shopware5;

use function dirname;
use FroshPluginUploader\Components\Generation\Shopware5\Plugin;
use FroshPluginUploader\Components\Generation\Shopware5\PluginReader;
use FroshPluginUploader\Components\PluginValidator\Shopware5\XmlChecker;
use FroshPluginUploader\Structs\ViolationContext;
use PHPUnit\Framework\TestCase;
use ZipArchive;

/**
 * @internal
 */
class XmlCheckerTest extends TestCase
{
    public function testCheckFailed(): void
    {
        $context = $this->makeContext();
        static::assertTrue((new XmlChecker())->supports($context));
        (new XmlChecker())->validate($context);

        static::assertTrue($context->hasViolations());
        static::assertContains('Plugin requires an plugin.xml', $context->getViolations());
        static::assertContains('Plugin requires an plugin.png', $context->getViolations());
        static::assertContains('Copyright is not defined in plugin.xml', $context->getViolations());
        static::assertContains('License is not defined in plugin.xml', $context->getViolations());
        static::assertContains('Author is not defined in plugin.xml', $context->getViolations());
        static::assertContains('Compatibility is not defined in plugin.xml', $context->getViolations());
        static::assertContains('Changelog is not defined in plugin.xml', $context->getViolations());
        static::assertContains('Label is not defined in plugin.xml', $context->getViolations());
        static::assertContains('Version is not defined in plugin.xml', $context->getViolations());
        static::assertContains('Description is not defined in plugin.xml', $context->getViolations());
    }

    public function testCheckSuccess(): void
    {
        $context = $this->makeContext([
            'copyright' => 'foo',
            'license' => 'foo',
            'author' => 'foo',
            'label' => 'foo',
            'version' => 'foo',
            'description' => 'foo',
            'compatibility' => 'foo',
            'changelog' => 'foo',
        ], dirname(__DIR__, 3) . '/fixtures/plugins/Shopware5Plugin/');
        static::assertTrue((new XmlChecker())->supports($context));
        (new XmlChecker())->validate($context);

        static::assertFalse($context->hasViolations());
    }

    private function makeContext(array $xml = [], string $dir = __DIR__): ViolationContext
    {
        $reader = $this->createMock(PluginReader::class);

        $reader->method('all')
            ->willReturn($xml)
        ;

        $plugin = $this->createMock(Plugin::class);

        $plugin->method('getReader')
            ->willReturn($reader)
        ;

        return new ViolationContext($plugin, new ZipArchive(), $dir);
    }
}
