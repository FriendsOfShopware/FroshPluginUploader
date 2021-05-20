<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\PluginValidator\Shopware6;

use FroshPluginUploader\Components\Generation\ShopwarePlatform\Plugin;
use FroshPluginUploader\Components\Generation\ShopwarePlatform\PluginReader;
use FroshPluginUploader\Components\PluginValidator\Shopware6\ThemeJsonValidator;
use FroshPluginUploader\Structs\ViolationContext;
use FroshPluginUploader\Tests\Components\IoHelper;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ThemeJsonValidatorTest extends TestCase
{
    public function testMissingThemeJson(): void
    {
        $context = $this->makeContext(null);

        static::assertTrue((new ThemeJsonValidator())->supports($context));
        (new ThemeJsonValidator())->validate($context);
        static::assertFalse($context->hasViolations());
    }

    public function testJsonInvalid(): void
    {
        $context = $this->makeContext('Test');

        static::expectException(\JsonException::class);
        static::assertTrue((new ThemeJsonValidator())->supports($context));
        (new ThemeJsonValidator())->validate($context);
    }

    public function testMissingPreviewImage(): void
    {
        $context = $this->makeContext('{}');

        static::assertTrue((new ThemeJsonValidator())->supports($context));
        (new ThemeJsonValidator())->validate($context);

        static::assertTrue($context->hasViolations());
        static::assertStringContainsString('Required field "previewMedia" in theme.json is not in', $context->getViolations()[0]);
    }

    public function testMissingFile(): void
    {
        $context = $this->makeContext('{"previewMedia": "logo.png"}');

        static::assertTrue((new ThemeJsonValidator())->supports($context));
        (new ThemeJsonValidator())->validate($context);

        static::assertTrue($context->hasViolations());
        static::assertStringContainsString('Theme preview image file is expected to be placed at', $context->getViolations()[0]);
    }

    public function testWithFile(): void
    {
        $context = $this->makeContext('{"previewMedia": "logo.png"}', true);

        static::assertTrue((new ThemeJsonValidator())->supports($context));
        (new ThemeJsonValidator())->validate($context);

        static::assertFalse($context->hasViolations());
    }

    private function makeContext(?string $json, bool $createImage = false): ViolationContext
    {
        $reader = $this->createMock(PluginReader::class);

        $plugin = $this->createMock(Plugin::class);

        $plugin->method('getReader')
            ->willReturn($reader)
        ;

        $plugin->method('getName')->willReturn('SwagTest');

        $folder = IoHelper::makeFolder();
        \mkdir($folder . '/SwagTest/src/Resources/', 0777, true);

        if ($json) {
            file_put_contents($folder . '/SwagTest/src/Resources/theme.json', $json);
        }

        if ($createImage) {
            file_put_contents($folder . '/SwagTest/src/Resources/logo.png', 'Test');
        }

        return new ViolationContext($plugin, new \ZipArchive(), $folder);
    }
}
