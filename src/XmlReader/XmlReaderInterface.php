<?php declare(strict_types=1);

namespace FroshPluginUploader\XmlReader;

interface XmlReaderInterface
{
    /**
     * @param string $xmlFile
     *
     * @return array
     */
    public function read(string $xmlFile): array;
}
