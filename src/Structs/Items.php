<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Items extends Struct
{
    /** @var string */
    public $name;

    /** @var string */
    public $label;

    /** @var bool */
    public $actual;

    /** @var bool */
    public $expected;

    /** @var bool */
    public $fulfilled;
}
