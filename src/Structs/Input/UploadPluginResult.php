<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs\Input;

class UploadPluginResult
{
    /**
     * @var bool
     */
    private $passed;

    /**
     * @var bool
     */
    private $hasWarnings;

    /**
     * @var string|null
     */
    private $message;

    public function __construct(bool $passed, bool $hasWarnings, ?string $message = null)
    {
        $this->passed = $passed;
        $this->hasWarnings = $hasWarnings;
        $this->message = $message;
    }

    public function isPassed(): bool
    {
        return $this->passed;
    }

    public function hasWarnings(): bool
    {
        return $this->hasWarnings;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
