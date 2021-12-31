<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class ImageDetail extends Struct
{
    public static $mappedFields = ['locale' => Locale::class];

    /** @var int */
    public $id;

    /** @var bool */
    public $preview;

    /** @var bool */
    public $activated;

    /** @var string */
    public $caption;

    /** @var Locale */
    public $locale;
}
