<?php
declare(strict_types=1);

use FroshPluginUploader\Commands\SelfUpdateCommand;
use FroshPluginUploader\Commands\ValidatePluginCommand;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Parameter;

return static function (ContainerConfigurator $configurator): void {
    $configurator->parameters()->set('project_dir', __DIR__ . '/../..');
    $configurator
        ->services()
        ->defaults()
        ->public()
        ->autowire()
        ->autoconfigure()
        ->load('FroshPluginUploader\\Commands\\', dirname(__DIR__) . '/Commands')
        ->load('FroshPluginUploader\\Components\\', dirname(__DIR__) . '/Components')
        ->exclude(dirname(__DIR__) . '/Components/{Generation,Generator,StoreJsonLoader.php,ZipStrategy}')
    ;

    $configurator
        ->services()
        ->get(ValidatePluginCommand::class)
        ->arg('$validators', new TaggedIteratorArgument('uploader.validation'))
    ;

    $configurator
        ->services()
        ->get(SelfUpdateCommand::class)
        ->arg('$currentVersion', new Parameter('app.version'))
    ;
};
