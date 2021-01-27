<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Picture extends Struct
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $remoteLink;

    /**
     * @var int
     */
    public $priority;
}
