<?php


namespace FroshPluginUploader\Structs\CodeReview;


use FroshPluginUploader\Structs\Struct;

class CodeReview extends Struct
{
    public static $mappedFields = ['type' => Type::class];

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $message;

    /**
     * @var integer
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