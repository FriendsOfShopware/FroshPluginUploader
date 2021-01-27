<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs\CodeReview;

use FroshPluginUploader\Structs\Struct;

class SubCheckResult extends Struct
{
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_SKIPPED = 'skipped';

    public static $mappedFields = [];

    /**
     * @var string
     */
    public $subCheck;

    /**
     * @var string
     */
    public $status;

    /**
     * @var bool
     */
    public $passed;

    /**
     * @var string
     */
    public $message;

    /**
     * @var bool
     */
    public $hasWarnings;
}
