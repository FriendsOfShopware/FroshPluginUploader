<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\ShopwarePlatform;

use Composer\Semver\Semver;
use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Components\PluginReaderInterface;
use FroshPluginUploader\Components\StoreJsonLoader;

class Plugin implements PluginInterface
{
    /**
     * @var string
     */
    private $rootFolder;
    /**
     * @var string
     */
    private $pluginName;

    public function __construct(string $rootFolder, string $pluginName)
    {
        $this->rootFolder = $rootFolder;
        $composerJson = json_decode(file_get_contents($rootFolder . '/composer.json'), true)['extra'];
        $className = explode('\\', $composerJson['shopware-plugin-class']);

        $this->pluginName = end($className);
    }

    public function getName(): string
    {
        return $this->pluginName;
    }

    public function getReader(): PluginReaderInterface
    {
        return new PluginReader($this->rootFolder);
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
        return $this->rootFolder . '/src/Resources/store/';
    }

    public function getRootDir(): string
    {
        return $this->rootFolder;
    }

    public function getCompatibleVersions(array $versions): array
    {
        $constraints = $this->getReader()->getCoreConstraint();
        $versions = array_values(array_filter($versions, function ($version) use ($constraints) {
            if ($version['major'] !== 'Shopware 6') {
                return null;
            }

            if (!$version['selectable']) {
                return null;
            }

            try {
                return Semver::satisfies($version['name'], $constraints);
            } catch (\UnexpectedValueException $e) {
                // EA is not semver compatible
                return null;
            }
        }));

        return $versions;
    }

    public function getStoreType(): string
    {
        return 'platform';
    }
}
