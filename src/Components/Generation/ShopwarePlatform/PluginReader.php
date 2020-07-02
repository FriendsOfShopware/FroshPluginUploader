<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\ShopwarePlatform;

use FroshPluginUploader\Components\PluginReaderInterface;

class PluginReader implements PluginReaderInterface
{
    private const REQUIRED_KEYS_COMPOSER_JSON = [
        'name',
        'type',
        'description',
        'license',
        'version',
        'authors',
        'require',
    ];

    private const REQUIRED_KEYS_EXTRA = [
        'label',
        'description',
    ];

    /**
     * @var array
     */
    private $composerJson = [];

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var ChangelogReader
     */
    private $changelogReader;

    public function __construct(string $path)
    {
        $this->composerJson = json_decode(file_get_contents($path . '/composer.json'), true);
        $this->rootDir = $path;
    }

    public function validate(): void
    {
        // Validate keys
        foreach (self::REQUIRED_KEYS_COMPOSER_JSON as $requiredKey) {
            if (!isset($this->composerJson[$requiredKey])) {
                throw new \RuntimeException(sprintf('%s is not defined in composer.json', ucfirst($requiredKey)));
            }
        }

        // Validate extra keys
        foreach (self::REQUIRED_KEYS_EXTRA as $requiredKey) {
            if (!isset($this->composerJson['extra'][$requiredKey])) {
                throw new \RuntimeException(sprintf('%s is not defined in composer.json extra section', ucfirst($requiredKey)));
            }
        }

        $wrappingFolderName = basename($this->rootDir);
        $pluginClassName = $this->getName();

        if ($wrappingFolderName !== $pluginClassName) {
            throw new \RuntimeException(sprintf('The folder is not wrapped inside a folder having the plugins name %s but %s instead', $pluginClassName, $wrappingFolderName));
        }

        // Call the changelog methods, it will throw a exception if they are missing
        $this->getNewestChangelogGerman();
        $this->getNewestChangelogEnglish();
    }

    public function getVersion(): string
    {
        return $this->composerJson['version'];
    }

    public function getNewestChangelogGerman(): string
    {
        return $this->getChangelogReader()->getChangelog('de-DE', $this->getVersion());
    }

    public function getNewestChangelogEnglish(): string
    {
        return $this->getChangelogReader()->getChangelog('en-GB', $this->getVersion());
    }

    public function getLabelGerman(): string
    {
        return $this->composerJson['extra']['label']['de-DE'];
    }

    public function getLabelEnglish(): string
    {
        return $this->composerJson['extra']['label']['en-GB'];
    }

    public function getDescriptionGerman(): string
    {
        return $this->composerJson['extra']['description']['de-DE'];
    }

    public function getDescriptionEnglish(): string
    {
        return $this->composerJson['extra']['description']['en-GB'];
    }

    public function getMinVersion(): string
    {
        return $this->composerJson['extra']['shopware-min-version'] ?? '6.0.0';
    }

    public function getMaxVersion(): ?string
    {
        return $this->composerJson['extra']['shopware-max-version'] ?? null;
    }

    public function getCoreConstraint()
    {
        return $this->composerJson['require']['shopware/core'];
    }

    public function getLicense(): string
    {
        return strtolower($this->composerJson['license']);
    }

    public function getName(): string
    {
        $fullyQualifiedPluginClassName = $this->composerJson['extra']['shopware-plugin-class'];

        return array_reverse(explode('\\', $fullyQualifiedPluginClassName))[0];
    }

    private function getChangelogReader(): ChangelogReader
    {
        if ($this->changelogReader === null) {
            $this->changelogReader = new ChangelogReader($this->rootDir);
        }

        return $this->changelogReader;
    }
}
