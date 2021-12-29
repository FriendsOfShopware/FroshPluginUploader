<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\XmlReader;

use DOMDocument;
use DOMElement;
use DOMXPath;

class XmlPluginReader extends XmlReaderBase
{
    public function __construct()
    {
        $this->xsdFile = __DIR__ . '/schema/plugin.xsd';
    }

    protected function parseFile(DOMDocument $xml): array
    {
        $xpath = new DOMXPath($xml);
        /** @var DOMElement $pluginData */
        $pluginData = $xpath->query('//plugin')->item(0);
        $info = [];

        if ($label = self::parseTranslatableNodeList($xpath->query('//plugin/label'))) {
            $info['label'] = $label;
        }

        if ($description = self::parseTranslatableNodeList($xpath->query('//plugin/description'))) {
            $info['description'] = $description;
        }

        foreach (['version', 'license', 'author', 'copyright', 'link'] as $simpleField) {
            if (($fieldValue = self::getElementChildValueByName($pluginData, $simpleField)) !== null) {
                $info[$simpleField] = $fieldValue;
            }
        }
        /** @var DOMElement $changelog */
        foreach ($pluginData->getElementsByTagName('changelog') as $changelog) {
            $version = $changelog->getAttribute('version');
            /** @var DOMElement $changes */
            foreach ($changelog->getElementsByTagName('changes') as $changes) {
                $lang = $changes->getAttribute('lang') ?: 'en';
                $info['changelog'][$version][$lang][] = $changes->nodeValue;
            }
        }
        $compatibility = $xpath->query('//plugin/compatibility')->item(0);
        if ($compatibility !== null) {
            $info['compatibility'] = [
                'minVersion' => $compatibility->getAttribute('minVersion'),
                'maxVersion' => $compatibility->getAttribute('maxVersion'),
            ];
        }

        return $info;
    }
}
