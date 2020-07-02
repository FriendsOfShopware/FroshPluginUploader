<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\Shopware5;

use FroshPluginUploader\Components\PluginReaderInterface;
use FroshPluginUploader\Components\XmlReader\XmlConfigReader;
use FroshPluginUploader\Components\XmlReader\XmlPluginReader;
use FroshPluginUploader\Exception\PluginValidationException;

class PluginReader implements PluginReaderInterface
{
    private const REQUIRED_KEYS = [
        'compatibility',
        'changelog',
        'label',
        'version',
        'license',
        'description',
    ];

    /**
     * @var array
     */
    private $xml;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $name;

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

        $this->name = basename($path);
    }

    public function validate(): void
    {
        // Validate keys
        foreach (self::REQUIRED_KEYS as $requiredKey) {
            if (!isset($this->xml[$requiredKey])) {
                throw new \RuntimeException(sprintf('%s is not defined in plugin.xml', ucfirst($requiredKey)));
            }
        }
    }

    public function getVersion(): string
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

    public function getLabelGerman(): string
    {
        return $this->xml['label']['de'];
    }

    public function getLabelEnglish(): string
    {
        return $this->xml['label']['en'];
    }

    public function getDescriptionGerman(): string
    {
        return $this->xml['description']['de'];
    }

    public function getDescriptionEnglish(): string
    {
        return $this->xml['description']['en'];
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

    public function getLicense(): string
    {
        return strtolower($this->xml['license']);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
