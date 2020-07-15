<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\ShopwarePlatform;

use FroshPluginUploader\Components\PluginReaderInterface;

class PluginReader implements PluginReaderInterface
{
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

    public function all(): array
    {
        return $this->composerJson;
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
