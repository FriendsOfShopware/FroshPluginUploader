<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs\CodeReview;

use FroshPluginUploader\Structs\Struct;

class SubCheckResult extends Struct
{
    public string $subCheck;

    public string $status;

    public bool $passed;

    public string $message;

    public bool $hasWarnings;
}
