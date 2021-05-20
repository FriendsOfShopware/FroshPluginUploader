<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\PluginValidator\ShopwareAppSystem;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin;
use FroshPluginUploader\Components\Generation\ShopwareApp\App;
use FroshPluginUploader\Components\Generation\ShopwareApp\AppReader;
use FroshPluginUploader\Components\PluginValidator\ShopwareAppSystem\ManifestChecker;
use FroshPluginUploader\Structs\ViolationContext;
use PHPUnit\Framework\TestCase;

class ManifestCheckerTest extends TestCase
{
    public function testEmptyManifest(): void
    {
        $context = $this->createContext();

        (new ManifestChecker())->validate($context);
        static::assertTrue($context->hasViolations());
        static::assertContains('"label" is not defined in manifest.xml', $context->getViolations());
        static::assertContains('"version" is not defined in manifest.xml', $context->getViolations());
        static::assertContains('"license" is not defined in manifest.xml', $context->getViolations());
    }

    public function testSupportsOnlyApp(): void
    {
        static::assertTrue((new ManifestChecker())->supports(new ViolationContext($this->createMock(App::class), new \ZipArchive(), __DIR__)));
        static::assertFalse((new ManifestChecker())->supports(new ViolationContext($this->createMock(Plugin::class), new \ZipArchive(), __DIR__)));
    }

    public function testValidManifest(): void
    {
        $context = $this->createContext(['label' => 'foo', 'version' => '1.0.0', 'license' => 'mit']);

        (new ManifestChecker())->validate($context);
        static::assertFalse($context->hasViolations());
    }

    private function createContext(array $config = []): ViolationContext
    {
        $reader = $this->createMock(AppReader::class);
        $reader->method('all')->willReturn($config);

        $plugin = $this->createMock(App::class);
        $plugin->method('getReader')->willReturn($reader);

        return new ViolationContext($plugin, new \ZipArchive(), __DIR__);
    }
}
