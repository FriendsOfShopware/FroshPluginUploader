<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class ImageDetail extends Struct
{
    /** @var int */
    public $id;

    /** @var bool */
    public $preview;

    /** @var bool */
    public $activated;

    /** @var string */
    public $caption;
    public static $mappedFields = [];

    /** @var Locale */
    public $locale;
}
