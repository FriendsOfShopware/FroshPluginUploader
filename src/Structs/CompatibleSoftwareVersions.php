<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class CompatibleSoftwareVersions extends Struct
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $parent;

    /** @var bool */
    public $selectable;

    /** @var string */
    public $major;

    /** @var null */
    public $releaseDate;

    /** @var bool */
    public $public;
}
