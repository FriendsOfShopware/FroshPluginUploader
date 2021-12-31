<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Contract extends Struct
{
    /** @var int */
    public $id;

    /** @var string */
    public $path;
}
