<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs\CodeReview;

use FroshPluginUploader\Structs\Struct;

class CodeReview extends Struct
{
    public static $mappedFields = ['type' => Type::class];

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $message;

    /**
     * @var int
     */
    public $binaryId;

    /**
     * @var string
     */
    public $creationDate;

    /**
     * @var Type
     */
    public $type;
}
