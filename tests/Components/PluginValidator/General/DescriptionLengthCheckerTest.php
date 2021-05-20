<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\General;

use FroshPluginUploader\Components\Generation\ShopwareApp\App;
use FroshPluginUploader\Components\Generation\ShopwarePlatform\Plugin;
use FroshPluginUploader\Components\Generation\ShopwarePlatform\PluginReader;
use FroshPluginUploader\Structs\ViolationContext;
use PHPUnit\Framework\TestCase;
use ZipArchive;

/**
 * @internal
 * @coversNothing
 */
class DescriptionLengthCheckerTest extends TestCase
{
    public function testSupportsPlatform(): void
    {
        $context = $this->makePluginContext(Plugin::class);
        static::assertTrue((new DescriptionLengthChecker())->supports($context));
    }

    public function testNotSupportsApps(): void
    {
        $context = $this->makePluginContext(App::class);
        static::assertFalse((new DescriptionLengthChecker())->supports($context));
    }

    public function testShortDescription(): void
    {
        $testDescription = 'Lorem Ipsum Cool Plugin';

        $context = $this->makeContext($testDescription);
        (new DescriptionLengthChecker())->validate($context);
        static::assertTrue($context->hasViolations());
        static::assertContains(sprintf('The German description with length of %s should have a length from 150 up to 185 characters.', mb_strlen($testDescription)), $context->getViolations());
        static::assertContains(sprintf('The English description with length of %s should have a length from 150 up to 185 characters.', mb_strlen($testDescription)), $context->getViolations());
    }

    public function testCorrectDescriptionLength(): void
    {
        $testDescription = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At v';

        $context = $this->makeContext($testDescription);
        (new DescriptionLengthChecker())->validate($context);
        static::assertFalse($context->hasViolations());
    }

    private function makeContext(string $description): ViolationContext
    {
        $reader = $this->createMock(PluginReader::class);

        $plugin = $this->createMock(Plugin::class);
        $plugin->method('getReader')->willReturn($reader);
        $reader->method('getDescriptionGerman')->willReturn($description);
        $reader->method('getDescriptionEnglish')->willReturn($description);

        return new ViolationContext($plugin, new ZipArchive(), __DIR__, null);
    }

    private function makePluginContext(string $plugin): ViolationContext
    {
        return new ViolationContext($this->createMock($plugin), new ZipArchive(), __DIR__, null);
    }
}
