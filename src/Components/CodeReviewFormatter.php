<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Structs\CodeReview\CodeReview;
use FroshPluginUploader\Structs\Input\UploadPluginResult;
use const PHP_EOL;

class CodeReviewFormatter
{
    public static function format(CodeReview $codeReview): UploadPluginResult
    {
        $message = '';
        $passed = $codeReview->type->id === 3 || $codeReview->type->name === 'automaticcodereviewsucceeded';
        $hasWarnings = false;

        foreach ($codeReview->subCheckResults as $subCheckResult) {
            if ($subCheckResult->passed && !$subCheckResult->hasWarnings) {
                continue;
            }

            $message .= sprintf('=== %s ===' . PHP_EOL, $subCheckResult->subCheck);
            $message .= strip_tags($subCheckResult->message);
            $message .= PHP_EOL . PHP_EOL;

            if ($subCheckResult->hasWarnings) {
                $hasWarnings = true;
            }
        }

        return new UploadPluginResult($passed, $hasWarnings, $message);
    }
}
