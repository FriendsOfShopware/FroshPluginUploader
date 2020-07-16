<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components\XmlReader;

use FroshPluginUploader\Components\XmlReader\XmlPluginReader;
use FroshPluginUploader\Components\XmlReader\XmlReaderBase;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class XmlReaderBaseTest extends TestCase
{
    public function testReadBrokenXml(): void
    {
        static::expectException(InvalidArgumentException::class);
        $reader = new XmlPluginReader();
        $reader->read(__DIR__ . '/broken.xml');
    }

    public function testParseTranslatableNodeList(): void
    {
        static::assertNull(XmlReaderBase::parseTranslatableNodeList(new \DOMNodeList()));
    }


    public function testGetNotExistingElement(): void
    {
        static::expectException(\InvalidArgumentException::class);
        XmlReaderBase::getElementChildValueByName(new \DOMElement('test', null, 'test'), 'foo', true);
    }
}