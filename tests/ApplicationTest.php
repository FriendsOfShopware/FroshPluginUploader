<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests;

use FroshPluginUploader\Application;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ApplicationTest extends TestCase
{
    public function testCliAppStart(): void
    {
        $app = new Application();
        static::assertSame('FroshPluginUploader', $app->getName());
    }
}
