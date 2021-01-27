<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\XmlReader;

interface XmlReaderInterface
{
    public function read(string $xmlFile): array;
}
