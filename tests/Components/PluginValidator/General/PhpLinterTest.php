<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\PluginValidator\General;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin;
use FroshPluginUploader\Components\PluginValidator\General\PhpLinter;
use FroshPluginUploader\Structs\ViolationContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PhpLinterTest extends TestCase
{
    public function testInvalidFolderLint(): void
    {
        $context = $this->createContext(dirname(__DIR__, 3) . '/fixtures/plugins/TestInvalidCode');
        static::assertTrue((new PhpLinter())->supports($context));

        (new PhpLinter())->validate($context);

        static::assertTrue($context->hasViolations());
        static::assertStringContainsString('Found syntax error in file', $context->getViolations()[0]);
    }

    public function testValidFolderLint(): void
    {
        $context = $this->createContext(dirname(__DIR__, 3) . '/fixtures/plugins/Shopware5Plugin');
        static::assertTrue((new PhpLinter())->supports($context));

        (new PhpLinter())->validate($context);

        static::assertFalse($context->hasViolations());
    }

    private function createContext(string $folder): ViolationContext
    {
        return new ViolationContext($this->createMock(Plugin::class), new \ZipArchive(), $folder);
    }
}
