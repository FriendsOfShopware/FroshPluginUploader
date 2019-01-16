<?php declare(strict_types=1);

namespace FroshPluginUploader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class DependencyInjection
{
    public static function getContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/Resources'));
        $loader->load('services.xml');

        $container->compile();

        return $container;
    }
}
