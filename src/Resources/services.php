<?php

use FroshPluginUploader\Commands\ValidatePluginCommand;
use FroshPluginUploader\Components\PluginZip;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator) {
    $configurator->parameters()->set('project_dir', __DIR__ . '/../..');
    $configurator
        ->services()
        ->defaults()
        ->public()
        ->autowire()
        ->autoconfigure()
        ->load('FroshPluginUploader\\Commands\\', dirname(__DIR__) . '/Commands')
        ->load('FroshPluginUploader\\Components\\', dirname(__DIR__) . '/Components')
        ->exclude(dirname(__DIR__) . '/Components/{Generation,Generator,StoreJsonLoader.php,ZipStrategy}');

    $configurator
        ->services()
        ->get(PluginZip::class)
        ->arg('$strategy', service('zip.strategy'));

    $configurator
        ->services()
        ->get(ValidatePluginCommand::class)
        ->arg('$validators', new TaggedIteratorArgument('uploader.validation'));

    $configurator
        ->services()
        ->set('zip.strategy')
        ->synthetic(true);
};
