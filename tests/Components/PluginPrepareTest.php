<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components;

use function dirname;
use FroshPluginUploader\Components\PluginPrepare;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @internal
 */
class PluginPrepareTest extends TestCase
{
    public function testZipShopware6Plugin(): void
    {
        $service = new PluginPrepare();
        $service->prepare(dirname(__DIR__) . '/fixtures/plugins/ShopwarePlatformPlugin', false, new NullOutput());
        static::assertDirectoryExists(
            dirname(__DIR__) . '/fixtures/plugins/ShopwarePlatformPlugin/vendor/symfony/web-link',
            'Cannot find the required composer dependencies'
        );
        exec('rm -rf ' . dirname(__DIR__) . '/fixtures/plugins/ShopwarePlatformPlugin/vendor');
        unlink(dirname(__DIR__) . '/fixtures/plugins/ShopwarePlatformPlugin/composer.lock');
    }
}
