<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Certification extends Struct
{
    /** @var int */
    public $pluginId;

    /** @var string */
    public $creationDate;

    /** @var BronzeResult */
    public $bronzeResult;

    /** @var SilverResult */
    public $silverResult;

    /** @var GoldResult */
    public $goldResult;

    /** @var Type */
    public $type;
}
