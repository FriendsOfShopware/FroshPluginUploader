<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Changelogs extends Struct
{
    /** @var int */
    public $id;

    /** @var Locale */
    public $locale;

    /** @var string */
    public $text;
}
