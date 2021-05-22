<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests;

use FroshPluginUploader\DependencyInjection;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DependencyInjectionTest extends TestCase
{
    public function testCompiled(): void
    {
        $container = DependencyInjection::getContainer();
        static::assertTrue($container->isCompiled());

        static::assertNotEmpty($container->findTaggedServiceIds('console.command'));
    }
}
