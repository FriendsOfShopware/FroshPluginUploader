<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

interface PluginInterface
{
    public function __construct(string $rootFolder, string $pluginName);

    public function getName(): string;

    public function getReader(): PluginReaderInterface;

    public function hasStoreJson(): bool;
    
    public function getStoreJson(): StoreJsonLoader;

    public function getResourcesFolderPath(): string;

    public function getRootDir(): string;

    public function getCompatibleVersions(array $versions): array;
}
