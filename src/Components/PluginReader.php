<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Exception\PluginValidationException;
use FroshPluginUploader\XmlReader\XmlConfigReader;
use FroshPluginUploader\XmlReader\XmlPluginReader;

class PluginReader
{
    private const REQUIRED_KEYS = [
        'compatibility',
        'changelog',
        'label',
        'version',
        'license',
        'description',
    ];

    private const LANGUAGE_FIELDS = [
        'label',
        'description',
    ];

    private const REQUIRED_LANGUAGES = [
        'de',
        'en',
    ];
    /**
     * @var array
     */
    private $xml;

    /**
     * @var array
     */
    private $config;

    public function __construct(string $path)
    {
        if (!file_exists($path . '/plugin.xml')) {
            throw new PluginValidationException('The plugin requires a plugin.xml');
        }

        $reader = new XmlPluginReader();
        $this->xml = $reader->read($path . '/plugin.xml');

        if (file_exists($path . '/Resources/config.xml')) {
            $configReader = new XmlConfigReader();
            $this->config = $configReader->read($path . '/Resources/config.xml');
        }
    }

    public function validate(): void
    {
        // Validate keys
        foreach (self::REQUIRED_KEYS as $requiredKey) {
            if (!isset($this->xml[$requiredKey])) {
                throw new \RuntimeException(sprintf('%s is not defined in plugin.xml', ucfirst($requiredKey)));
            }
        }

        // Validate language in keys
        foreach (self::LANGUAGE_FIELDS as $requiredKey) {
            foreach (self::REQUIRED_LANGUAGES as $language) {
                if (!isset($this->xml[$requiredKey][$language])) {
                    throw new \RuntimeException(sprintf('%s with language %s is not defined in plugin.xml', ucfirst($requiredKey), $language));
                }
            }
        }

        $latestChangelog = $this->xml['changelog'][$this->xml['version']];

        foreach (self::REQUIRED_LANGUAGES as $language) {
            if (!isset($latestChangelog[$language])) {
                throw new \RuntimeException(sprintf('Changelog for version %s has no translation for %s', $this->xml['version'], $language));
            }
        }

        $this->validateConfig();
    }

    public function getVersion()
    {
        return $this->xml['version'];
    }

    public function getNewestChangelogGerman(): string
    {
        $data = $this->xml['changelog'][$this->xml['version']]['de'];

        if (is_array($data)) {
            $data = implode('', $data);
        }

        return $data;
    }

    public function getNewestChangelogEnglish(): string
    {
        $data = $this->xml['changelog'][$this->xml['version']]['en'];

        if (is_array($data)) {
            $data = implode('', $data);
        }

        return $data;
    }

    public function getMinVersion(): string
    {
        if (!empty($this->xml['compatibility']['minVersion'])) {
            return $this->xml['compatibility']['minVersion'];
        }

        return '5.2.0';
    }

    public function getMaxVersion(): ?string
    {
        if (!empty($this->xml['compatibility']['maxVersion'])) {
            return $this->xml['compatibility']['maxVersion'];
        }

        return null;
    }

    private function validateConfig(): void
    {
        if (empty($this->config)) {
            return;
        }

        foreach ($this->config['elements'] as $element) {
            foreach (self::LANGUAGE_FIELDS as $requiredKey) {
                if (!isset($element[$requiredKey])) {
                    continue;
                }

                foreach (self::REQUIRED_LANGUAGES as $language) {
                    if (!isset($element[$requiredKey][$language])) {
                        throw new \RuntimeException(sprintf('Element name: %s, Attribute: %s with language %s is not defined in config.xml', $element['name'], $requiredKey, $language));
                    }
                }
            }
        }
    }
}
