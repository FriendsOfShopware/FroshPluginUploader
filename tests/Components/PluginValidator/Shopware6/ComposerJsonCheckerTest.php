<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\PluginValidator\Shopware6;

use FroshPluginUploader\Components\Generation\ShopwarePlatform\Plugin;
use FroshPluginUploader\Components\Generation\ShopwarePlatform\PluginReader;
use FroshPluginUploader\Components\PluginValidator\Shopware6\ComposerJsonChecker;
use FroshPluginUploader\Structs\ViolationContext;
use FroshPluginUploader\Tests\Components\IoHelper;
use PHPUnit\Framework\TestCase;

class ComposerJsonCheckerTest extends TestCase
{
    public function testEmptyConfig(): void
    {
        $context = $this->makeContext();

        static::assertTrue((new ComposerJsonChecker())->supports($context));
        (new ComposerJsonChecker())->validate($context);

        static::assertTrue($context->hasViolations());
        static::assertContains('"name" is not defined in composer.json', $context->getViolations());
        static::assertContains('"type" is not defined in composer.json', $context->getViolations());
        static::assertContains('"description" is not defined in composer.json', $context->getViolations());
        static::assertContains('"license" is not defined in composer.json', $context->getViolations());
        static::assertContains('"version" is not defined in composer.json', $context->getViolations());
        static::assertContains('"require" is not defined in composer.json', $context->getViolations());
        static::assertContains('"extra" section is missing in the composer.json', $context->getViolations());
        static::assertContains('At least one of the properties psr-0 or psr-4 are required in the composer.json', $context->getViolations());
        static::assertContains('"type" should be "shopware-platform-plugin"', $context->getViolations());
        static::assertContains('You need to require "shopware/core" package', $context->getViolations());
    }

    public function testValidButNotTranslated(): void
    {
        $context = $this->makeContext([
            'name' => 'swag/test',
            'type' => 'shopware-platform-plugin',
            'description' => 'Foo',
            'license' => 'Foo',
            'version' => '1.0.0',
            'authors' => [
                [
                    'name' => 'Test'
                ]
            ],
            "autoload" => [
                'psr-4' => [
                    'Swag\\Test\\' => 'src/'
                ]
            ],
            'require' => [
                'shopware/core' => '*'
            ],
            'extra' => [
                'shopware-plugin-class' => 'Swag\\Test\\SwagTest',
                'label' => [
                    'de-DE' => 'FOO',
                ],
                'description' => [
                    'de-DE' => 'FOO',
                ],
                'manufacturerLink' => [
                    'de-DE' => 'FOO',
                ],
                'supportLink' => [
                    'de-DE' => 'FOO'
                ]
            ]
        ]);

        static::assertTrue((new ComposerJsonChecker())->supports($context));
        (new ComposerJsonChecker())->validate($context);

        static::assertTrue($context->hasViolations());
        static::assertContains('"label" in "extra" needs an translation for en-GB', $context->getViolations());
        static::assertContains('"description" in "extra" needs an translation for en-GB', $context->getViolations());
    }

    public function testValid(): void
    {
        $context = $this->makeContext([
            'name' => 'swag/test',
            'type' => 'shopware-platform-plugin',
            'description' => 'Foo',
            'license' => 'Foo',
            'version' => '1.0.0',
            'authors' => [
                [
                    'name' => 'Test'
                ]
            ],
            "autoload" => [
                'psr-4' => [
                    'Swag\\Test\\' => 'src/'
                ]
            ],
            'require' => [
                'shopware/core' => '*'
            ],
            'extra' => [
                'shopware-plugin-class' => 'Swag\\Test\\SwagTest',
                'label' => [
                    'de-DE' => 'FOO',
                    'en-GB' => 'FOO'
                ],
                'description' => [
                    'de-DE' => 'FOO',
                    'en-GB' => 'FOO'
                ],
                'manufacturerLink' => [
                    'de-DE' => 'FOO',
                ],
                'supportLink' => [
                    'de-DE' => 'FOO'
                ]
            ]
        ]);

        static::assertTrue((new ComposerJsonChecker())->supports($context));
        (new ComposerJsonChecker())->validate($context);

        static::assertFalse($context->hasViolations());
    }

    public function testValidPsr0(): void
    {
        $context = $this->makeContext([
            'name' => 'swag/test',
            'type' => 'shopware-platform-plugin',
            'description' => 'Foo',
            'license' => 'Foo',
            'version' => '1.0.0',
            'authors' => [
                [
                    'name' => 'Test'
                ]
            ],
            "autoload" => [
                'psr-0' => [
                    'Swag\\Test\\' => 'src/'
                ]
            ],
            'require' => [
                'shopware/core' => '*'
            ],
            'extra' => [
                'shopware-plugin-class' => 'Swag\\Test\\SwagTest',
                'label' => [
                    'de-DE' => 'FOO',
                    'en-GB' => 'FOO'
                ],
                'description' => [
                    'de-DE' => 'FOO',
                    'en-GB' => 'FOO'
                ],
                'manufacturerLink' => [
                    'de-DE' => 'FOO',
                ],
                'supportLink' => [
                    'de-DE' => 'FOO'
                ]
            ]
        ]);

        static::assertTrue((new ComposerJsonChecker())->supports($context));
        (new ComposerJsonChecker())->validate($context);

        static::assertFalse($context->hasViolations());
    }

    public function testInvalidPluginClass(): void
    {
        $context = $this->makeContext([
            'name' => 'swag/test',
            'type' => 'shopware-platform-plugin',
            'description' => 'Foo',
            'license' => 'Foo',
            'version' => '1.0.0',
            'authors' => [
                [
                    'name' => 'Test'
                ]
            ],
            "autoload" => [
                'psr-4' => [
                    'Swag\\Test\\' => 'src/'
                ]
            ],
            'require' => [
                'shopware/core' => '*'
            ],
            'extra' => [
                'shopware-plugin-class' => 'Foo\\Test\\SwagTest',
                'label' => [
                    'de-DE' => 'FOO',
                    'en-GB' => 'FOO'
                ],
                'description' => [
                    'de-DE' => 'FOO',
                    'en-GB' => 'FOO'
                ],
                'manufacturerLink' => [
                    'de-DE' => 'FOO',
                ],
                'supportLink' => [
                    'de-DE' => 'FOO'
                ]
            ]
        ]);

        static::assertTrue((new ComposerJsonChecker())->supports($context));
        (new ComposerJsonChecker())->validate($context);

        static::assertTrue($context->hasViolations());
        static::assertContains('Cannot find plugin bootstrap file in zip at path "SwagTest/SwagTest.php"', $context->getViolations());
    }

    private function makeContext(array $xml = [], string $pluginName = 'SwagTest'): ViolationContext
    {
        $reader = $this->createMock(PluginReader::class);

        $reader->method('all')
            ->willReturn($xml);

        $plugin = $this->createMock(Plugin::class);

        $plugin->method('getReader')
            ->willReturn($reader);

        $plugin->method('getName')->willReturn($pluginName);

        $zip = IoHelper::makeZip();
        $zip->addFromString('SwagTest/src/SwagTest.php', 'Test');

        return new ViolationContext($plugin, $zip, __DIR__);
    }
}