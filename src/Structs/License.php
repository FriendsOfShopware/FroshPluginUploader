<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class License extends Struct
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $description;
}
