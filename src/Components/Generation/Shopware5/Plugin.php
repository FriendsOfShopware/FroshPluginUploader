<?php /** @noinspection PhpMissingFieldTypeInspection */
/** @noinspection PhpMissingFieldTypeInspection */
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\Shopware5;

use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Components\PluginReaderInterface;
use FroshPluginUploader\Components\StoreJsonLoader;

class Plugin implements PluginInterface
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var PluginReader
     */
    private $reader;

    public function __construct(string $rootFolder, string $pluginName)
    {
        $this->rootDir = $rootFolder;
        $this->pluginName = $pluginName;
        $this->reader = new PluginReader($this->rootDir);
    }

    public function getName(): string
    {
        return $this->pluginName;
    }

    public function getReader(): PluginReaderInterface
    {
        return $this->reader;
    }

    public function hasStoreJson(): bool
    {
        return file_exists($this->getResourcesFolderPath() . 'store.json');
    }

    public function getStoreJson(): StoreJsonLoader
    {
        return new StoreJsonLoader($this->getResourcesFolderPath() . 'store.json');
    }

    public function getResourcesFolderPath(): string
    {
        return $this->rootDir . '/Resources/store/';
    }

    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    public function getCompatibleVersions(array $versions): array
    {
        $minVersion = $this->reader->getMinVersion();
        $maxVersion = $this->reader->getMaxVersion();
        $matches = [];

        foreach ($versions as $version) {
            if (!$version['selectable']) {
                continue;
            }

            if ($version['major'] !== 'Shopware 5') {
                continue;
            }

            $versionName = $version['name'];

            /** @var string[] $versionSplit */
            $versionSplit = explode('-', $versionName);
            if (empty($versionSplit[0])) {
                continue;
            }

            $versionName = $versionSplit[0];
            if (version_compare($versionName, $minVersion, '>=') && ($maxVersion === null || version_compare($versionName, $maxVersion, '<='))) {
                $version['children'] = [];
                $matches[] = $version;
            }
        }

        return $matches;
    }

    public function getStoreType(): string
    {
        return 'classic';
    }
}
