<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Details extends Struct
{
    /** @var int */
    public $id;

    public static $mappedFields = [];

    /** @var Locale */
    public $locale;

    /** @var string */
    public $description;
}
