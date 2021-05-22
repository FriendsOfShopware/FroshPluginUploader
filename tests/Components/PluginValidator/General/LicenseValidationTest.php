<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\PluginValidator\General;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin;
use FroshPluginUploader\Components\Generation\Shopware5\PluginReader;
use FroshPluginUploader\Components\PluginValidator\General\LicenseValidation;
use FroshPluginUploader\Structs\ViolationContext;
use PHPUnit\Framework\TestCase;
use ZipArchive;

/**
 * @internal
 */
class LicenseValidationTest extends TestCase
{
    public function testValidCase(): void
    {
        $context = $this->makeContext('MIT', 'MIT');
        static::assertTrue((new LicenseValidation())->supports($context));
        (new LicenseValidation())->validate($context);
        static::assertFalse($context->hasViolations());
    }

    public function testInvalidLicense(): void
    {
        $context = $this->makeContext('this system blows you', '');
        (new LicenseValidation())->validate($context);
        static::assertTrue($context->hasViolations());
        static::assertContains('The license must comply with a valid open-source identifier or `proprietary`. https://spdx.org/licenses', $context->getViolations());
    }

    public function testZipAndStoreDoesNotMatch(): void
    {
        $context = $this->makeContext('mit', 'proprietary');
        (new LicenseValidation())->validate($context);
        static::assertTrue($context->hasViolations());
        static::assertContains('The account plugin license does not match the license from the zipped plugin', $context->getViolations());
    }

    private function makeContext(string $zipLicense, string $storeLicense): ViolationContext
    {
        $reader = $this->createMock(PluginReader::class);
        $reader->method('getLicense')->willReturn($zipLicense);

        $plugin = $this->createMock(Plugin::class);
        $plugin->method('getReader')
            ->willReturn($reader)
        ;

        $storePlugin = new \FroshPluginUploader\Structs\Plugin();
        $storePlugin->license = new \FroshPluginUploader\Structs\License();
        $storePlugin->license->name = $storeLicense;

        return new ViolationContext($plugin, new ZipArchive(), __DIR__, $storePlugin);
    }
}
