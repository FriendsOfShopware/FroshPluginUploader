<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs\CodeReview;

use FroshPluginUploader\Structs\Struct;

class CodeReview extends Struct
{
    public static $mappedFields = [
        'type' => Type::class,
        'subCheckResults' => SubCheckResult::class,
    ];

    public int $id;

    public string $message;

    public int $binaryId;

    public string $creationDate;

    public Type $type;

    public array $subCheckResults;
}
