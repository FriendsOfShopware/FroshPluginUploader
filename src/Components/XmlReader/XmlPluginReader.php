<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\XmlReader;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;

class XmlPluginReader extends XmlReaderBase
{
    /**
     * @var string
     */
    protected $xsdFile = __DIR__ . '/schema/plugin.xsd';

    /**
     * parse required plugin blacklist
     *
     * @return array|null
     */
    public static function parseBlacklist(DOMNodeList $items)
    {
        if ($items->length === 0) {
            return null;
        }
        $blacklist = [];
        /** @var DOMElement $item */
        foreach ($items as $item) {
            $blacklist[] = $item->nodeValue;
        }

        return $blacklist;
    }

    /**
     * This method should be overridden as main entry point to parse a xml file.
     *
     * @return array
     */
    protected function parseFile(DOMDocument $xml)
    {
        $xpath = new DOMXPath($xml);
        $plugin = $xpath->query('//plugin');
        /** @var DOMElement $pluginData */
        $pluginData = $plugin->item(0);
        $info = [];

        if ($label = self::parseTranslatableNodeList($xpath->query('//plugin/label'))) {
            $info['label'] = $label;
        }

        if ($description = self::parseTranslatableNodeList($xpath->query('//plugin/description'))) {
            $info['description'] = $description;
        }

        $simpleFields = ['version', 'license', 'author', 'copyright', 'link'];
        foreach ($simpleFields as $simpleField) {
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
                'blacklist' => self::parseBlacklist(
                    $compatibility->getElementsByTagName('blacklist')
                ),
            ];
        }
        $requiredPlugins = self::getFirstChildren(
            $pluginData,
            'requiredPlugins'
        );
        if ($requiredPlugins !== null) {
            $info['requiredPlugins'] = $this->parseRequiredPlugins($requiredPlugins);
        }

        return $info;
    }

    /**
     * parse required plugins
     *
     * @return array
     */
    private function parseRequiredPlugins(DOMElement $requiredPluginNode)
    {
        $plugins = [];
        $requiredPlugins = $requiredPluginNode->getElementsByTagName('requiredPlugin');
        /** @var DOMElement $requiredPlugin */
        foreach ($requiredPlugins as $requiredPlugin) {
            $plugin = [];
            $plugin['pluginName'] = $requiredPlugin->getAttribute('pluginName');
            if ($minVersion = $requiredPlugin->getAttribute('minVersion')) {
                $plugin['minVersion'] = $minVersion;
            }
            if ($maxVersion = $requiredPlugin->getAttribute('maxVersion')) {
                $plugin['maxVersion'] = $maxVersion;
            }
            $blacklist = self::parseBlacklist(
                $requiredPlugin->getElementsByTagName('blacklist')
            );
            if ($blacklist !== null) {
                $plugin['blacklist'] = $blacklist;
            }
            $plugins[] = $plugin;
        }

        return $plugins;
    }
}
