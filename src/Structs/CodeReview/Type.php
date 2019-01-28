<?php

namespace FroshPluginUploader\Structs\CodeReview;


use FroshPluginUploader\Structs\Struct;

class Type extends Struct
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $description;
}