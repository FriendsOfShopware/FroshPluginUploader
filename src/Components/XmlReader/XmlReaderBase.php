<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\XmlReader;

use function assert;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use Exception;
use InvalidArgumentException;
use function is_array;
use Symfony\Component\Config\Util\XmlUtils;

/**
 * Class XmlReaderBase
 */
abstract class XmlReaderBase implements XmlReaderInterface
{
    private const DEFAULT_LANG = 'en';

    protected ?string $xsdFile = null;

    /**
     * load and validate xml file - parse to array
     */
    public function read(string $xmlFile): array
    {
        try {
            $dom = XmlUtils::loadFile($xmlFile, $this->xsdFile);
        } catch (Exception $e) {
            throw new InvalidArgumentException(sprintf('Unable to parse file "%s".', $xmlFile), $e->getCode(), $e);
        }

        return $this->parseFile($dom);
    }

    /**
     * Parses translatable node list.
     */
    public static function parseTranslatableNodeList(DOMNodeList $list): ?array
    {
        if ($list->length === 0) {
            return null;
        }
        $translations = [];
        /** @var DOMElement $item */
        foreach ($list as $item) {
            $language = $item->getAttribute('lang') ?: self::DEFAULT_LANG;
            // XSD Requires en-GB, Zend uses en_GB
            $language = str_replace('-', '_', $language);
            assert(!is_array($language));
            $translations[$language] = trim($item->nodeValue);
        }

        return $translations;
    }

    /**
     * Returns all element child values by nodeName.
     *
     *@throws InvalidArgumentException
     */
    public static function getElementChildValueByName(DOMElement $element, string $name, bool $throwException = false): ?string
    {
        $children = $element->getElementsByTagName($name);
        if ($children->length === 0) {
            if ($throwException) {
                throw new InvalidArgumentException(sprintf('Element with %s not found', $name));
            }

            return null;
        }

        return $children->item(0)->nodeValue;
    }

    /**
     * This method should be overridden as main entry point to parse a xml file.
     */
    abstract protected function parseFile(DOMDocument $xml): array;
}
