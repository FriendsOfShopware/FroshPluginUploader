<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\XmlReader;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use InvalidArgumentException;

class XmlConfigReader extends XmlReaderBase
{
    protected $xsdFile = __DIR__ . '/schema/config.xsd';

    /**
     * Validates scope attribute.
     *
     * @param string $scope
     *
     * @return int
     */
    public static function validateAttributeScope($scope)
    {
        if ($scope === '' || $scope === 'locale') {
            return self::SCOPE_LOCALE;
        }
        if ($scope === 'shop') {
            return self::SCOPE_SHOP;
        }
        throw new InvalidArgumentException(sprintf("Invalid scope '%s", $scope));
    }

    /**
     * This method should be overridden as main entry point to parse a xml file.
     *
     * @return array
     */
    protected function parseFile(DOMDocument $xml)
    {
        $xpath = new \DOMXPath($xml);
        $form = [];
        $form['label'] = self::parseTranslatableNodeList(
            $xpath->query('//config/label')
        );
        $form['description'] = self::parseTranslatableNodeList(
            $xpath->query('//config/description')
        );
        $form['elements'] = $this->parseElementNodeList(
            $xpath->query('//config/elements/element')
        );

        return $form;
    }

    /**
     * parses DOMNodeList with elements
     *
     * @return array
     */
    private function parseElementNodeList(DOMNodeList $list)
    {
        if ($list->length === 0) {
            return [];
        }
        $elements = [];
        /** @var DOMElement $item */
        foreach ($list as $item) {
            $element = [];
            //attributes
            $element['scope'] = self::validateAttributeScope(
                $item->getAttribute('scope')
            );
            $element['isRequired'] = self::validateBooleanAttribute(
                $item->getAttribute('required'),
                false
            );
            $element['type'] = self::validateTextAttribute(
                $item->getAttribute('type'),
                'text'
            );
            //elements
            if ($name = $item->getElementsByTagName('name')->item(0)) {
                $element['name'] = $name->nodeValue;
            }
            if ($value = $item->getElementsByTagName('value')->item(0)) {
                $element['value'] = $value->nodeValue;
            }
            $element['label'] = self::parseTranslatableNodeList(
                $item->getElementsByTagName('label')
            );
            $element['description'] = self::parseTranslatableNodeList(
                $item->getElementsByTagName('description')
            );
            $element['options'] = [];
            if ($options = self::parseOptionsNodeList(
                $item->getElementsByTagName('options')
            )) {
                $element['options'] = $options;
            }
            $elements[] = $element;
        }

        return $elements;
    }
}
