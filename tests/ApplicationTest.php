<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests;

use FroshPluginUploader\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class ApplicationTest extends TestCase
{
    public function testCliAppStart(): void
    {
        $app = new Application();
        static::assertTrue($app->getContainer()->isCompiled());
        static::assertInstanceOf(Command::class, $app->find('ext:update'));
    }
}
