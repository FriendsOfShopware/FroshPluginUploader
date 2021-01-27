<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\Shopware5;

use FroshPluginUploader\Components\PluginReaderInterface;
use FroshPluginUploader\Components\XmlReader\XmlPluginReader;

class PluginReader implements PluginReaderInterface
{
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
        $reader = new XmlPluginReader();
        $this->xml = $reader->read($path . '/plugin.xml');

        $this->name = basename($path);
    }

    public function all(): array
    {
        return $this->xml;
    }

    public function getVersion(): string
    {
        return $this->xml['version'];
    }

    public function getNewestChangelogGerman(): string
    {
        $data = $this->xml['changelog'][$this->xml['version']]['de'] ?? $this->xml['changelog'][$this->xml['version']]['en'];

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
        return $this->xml['label']['de'] ?? $this->xml['label']['en'];
    }

    public function getLabelEnglish(): string
    {
        return $this->xml['label']['en'];
    }

    public function getDescriptionGerman(): string
    {
        return $this->xml['description']['de'] ?? $this->getDescriptionEnglish();
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
