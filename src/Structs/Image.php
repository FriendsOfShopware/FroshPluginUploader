<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Image extends Struct
{
    /** @var int */
    public $id;

    /** @var string */
    public $remoteLink;

    /** @var ImageDetail[] */
    public $details;

    /** @var int */
    public $priority;

    public static $mappedFields = ['details' => ImageDetail::class];
}
