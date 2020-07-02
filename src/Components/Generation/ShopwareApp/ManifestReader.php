<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\ShopwareApp;

use DOMDocument;
use DOMXPath;
use FroshPluginUploader\Components\XmlReader\XmlReaderBase;

class ManifestReader extends XmlReaderBase
{
    protected function parseFile(DOMDocument $xml)
    {
        $xpath = new DOMXPath($xml);
        $info = [];

        if ($label = self::parseTranslatableNodeList($xpath->query('//manifest/meta/label'))) {
            $info['label'] = $label;
        }

        $meta = $xpath->query('//manifest/meta')[0];

        $info['name'] = self::getElementChildValueByName($meta, 'name');
        $info['version'] = self::getElementChildValueByName($meta, 'version');

        return $info;
    }
}
