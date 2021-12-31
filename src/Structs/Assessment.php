<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

/** @noinspection PhpMissingFieldTypeInspection */
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

    /** @var string */
    public $assessmentDate;
}
