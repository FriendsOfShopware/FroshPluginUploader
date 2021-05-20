<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\PluginValidator\General;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin;
use FroshPluginUploader\Components\Generation\Shopware5\PluginReader;
use FroshPluginUploader\Components\PluginValidator\General\VersionChecker;
use FroshPluginUploader\Structs\ViolationContext;
use PHPUnit\Framework\TestCase;
use ZipArchive;

/**
 * @internal
 * @coversNothing
 */
class VersionCheckerTest extends TestCase
{
    public function testVersion(): void
    {
        $context = $this->makeContext('1.0.0');
        static::assertTrue((new VersionChecker())->supports($context));

        (new VersionChecker())->validate($context);

        static::assertFalse($context->hasViolations());
    }

    public function testInvalidVersion(): void
    {
        $context = $this->makeContext('FOOO');
        (new VersionChecker())->validate($context);

        static::assertTrue($context->hasViolations());
        static::assertContains('Semver validation of plugin version has been failed with "Invalid version string "FOOO""', $context->getViolations());
    }

    private function makeContext(string $version): ViolationContext
    {
        $reader = $this->createMock(PluginReader::class);
        $reader->method('getVersion')->willReturn($version);

        $plugin = $this->createMock(Plugin::class);
        $plugin->method('getReader')
            ->willReturn($reader)
        ;

        return new ViolationContext($plugin, new ZipArchive(), __DIR__);
    }
}
