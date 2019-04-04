<?php
/**
 * Created by PhpStorm.
 * User: dwayne.sharp
 * Date: 04.04.2019
 * Time: 12:21
 */

namespace FroshPluginUploader\Structs\Config;

use FroshPluginUploader\Structs\Struct;

class PluginConfig extends Struct
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $version;

    /**
     * @var string
     */
    public $lastChange;

    /**
     * @var string
     */
    public $status;
}