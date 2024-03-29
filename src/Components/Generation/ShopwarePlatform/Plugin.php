<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\ShopwarePlatform;

use function assert;
use Composer\Semver\Semver;
use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Components\PluginReaderInterface;
use FroshPluginUploader\Components\StoreJsonLoader;
use const JSON_THROW_ON_ERROR;
use UnexpectedValueException;

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

    /** @noinspection PhpUnusedParameterInspection */
    public function __construct(string $rootFolder, string $pluginName)
    {
        $this->rootFolder = $rootFolder;
        $composerJson = json_decode(file_get_contents($rootFolder . '/composer.json'), true, 512, JSON_THROW_ON_ERROR)['extra'];
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

    /** @noinspection BadExceptionsProcessingInspection */
    public function getCompatibleVersions(array $versions): array
    {
        $reader = $this->getReader();
        assert($reader instanceof PluginReader);
        $constraints = $reader->getCoreConstraint();

        return array_values(array_filter($versions, static function ($version) use ($constraints) {
            if ($version['major'] !== 'Shopware 6') {
                return null;
            }

            if (!$version['selectable']) {
                return null;
            }

            try {
                return Semver::satisfies($version['name'], $constraints);
            } catch (UnexpectedValueException $e) {
                // EA is not semver compatible
                return null;
            }
        }));
    }

    public function getStoreType(): string
    {
        return 'platform';
    }
}
