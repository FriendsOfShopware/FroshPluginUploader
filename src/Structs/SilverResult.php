<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class SilverResult extends Struct
{
    /** @var Items[] */
    public $items;

    /** @var bool */
    public $fulfilled;
}
