<?php
declare(strict_types=1);

namespace FroshPluginUploader;

use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class DependencyInjection
{
    public static function getContainer(string $currentVersion): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->registerForAutoconfiguration(Command::class)->addTag('console.command');
        $container->registerForAutoconfiguration(ValidationInterface::class)->addTag('uploader.validation');
        $container->setParameter('app.version', $currentVersion);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/Resources'));
        $loader->load('services.php');
        $container->compile();

        return $container;
    }
}
