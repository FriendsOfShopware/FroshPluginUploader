<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class GoldResult extends Struct
{
    /** @var Items[] */
    public $items;

    /** @var bool */
    public $fulfilled;
}
