<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class MainCategory extends Struct
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var bool */
    public $parent;

    /** @var int */
    public $position;

    /** @var bool */
    public $public;

    /** @var bool */
    public $visible;

    /** @var bool */
    public $suggested;

    /** @var bool */
    public $applicable;

    /** @var Details[] */
    public $details;

    /** @var bool */
    public $active;
}
