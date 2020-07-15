<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\General;

use Composer\Semver\VersionParser;
use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Structs\ViolationContext;

class VersionChecker implements ValidationInterface
{
    public function supports(ViolationContext $context): bool
    {
        return true;
    }

    public function validate(ViolationContext $context): void
    {
        try {
            (new VersionParser())->normalize($context->getPlugin()->getReader()->getVersion());
        } catch (\UnexpectedValueException $e) {
            $context->addViolation(sprintf('Semver validation of plugin version has been failed with "%s"', $e->getMessage()));
        }
    }
}