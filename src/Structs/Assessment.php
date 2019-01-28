<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Assessment extends Struct
{
    /** @var int */
    public $id;

    /** @var int */
    public $baseValue;

    /** @var int */
    public $resultValue;

    /** @var string */
    public $comment;

    public static $mappedFields = [];

    /** @var Factors */
    public $factors;

    /** @var string */
    public $assessmentDate;
}
