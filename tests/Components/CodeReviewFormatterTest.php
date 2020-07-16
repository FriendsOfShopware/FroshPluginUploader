<?php declare(strict_types=1);

namespace FroshPluginUploader\Tests\Components;

use FroshPluginUploader\Components\CodeReviewFormatter;
use FroshPluginUploader\Structs\CodeReview\CodeReview;
use FroshPluginUploader\Structs\CodeReview\SubCheckResult;
use FroshPluginUploader\Structs\CodeReview\Type;
use PHPUnit\Framework\TestCase;

class CodeReviewFormatterTest extends TestCase
{
    public function testNotPassed(): void
    {
        $codeReview = new CodeReview();
        $codeReview->type = new Type();
        $codeReview->type->id = 1;
        $codeReview->type->description = 'failed';
        $codeReview->subCheckResults = [];

        $formatted = CodeReviewFormatter::format($codeReview);
        static::assertFalse($formatted->isPassed());
        static::assertFalse($formatted->hasWarnings());
    }

    public function testPassedWithWarnings(): void
    {
        $codeReview = CodeReview::make([
            'type' => [
                'id' => 3,
                'description' => 'automaticcodereviewsucceeded'
            ],
            'subCheckResults' => [
                [
                    'subCheck' => 'test',
                    'passed' => true,
                    'hasWarnings' => true,
                    'message' => 'Warning',
                ],
                [
                    'passed' => true,
                    'hasWarnings' => false,
                    'message' => 'test',
                ]
            ]
        ]);

        $formatted = CodeReviewFormatter::format($codeReview);
        static::assertTrue($formatted->isPassed());
        static::assertTrue($formatted->hasWarnings());
        static::assertSame('=== test ===
Warning

', $formatted->getMessage());
    }
}