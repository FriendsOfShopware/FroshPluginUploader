<?php

use FroshPluginUploader\Commands\ValidatePluginCommand;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function(ContainerConfigurator $configurator) {
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
        ->get(ValidatePluginCommand::class)
        ->arg('$validators', new TaggedIteratorArgument('uploader.validation'));
};