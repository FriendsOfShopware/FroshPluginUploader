<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator;

use FroshPluginUploader\Structs\ViolationContext;

interface ValidationInterface
{
    public function supports(ViolationContext $context): bool;

    public function validate(ViolationContext $context): void;
}
