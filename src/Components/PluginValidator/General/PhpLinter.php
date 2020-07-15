<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\General;

use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Structs\ViolationContext;

class PhpLinter implements ValidationInterface
{
    public function supports(ViolationContext $context): bool
    {
        return true;
    }

    public function validate(ViolationContext $context): void
    {
        $cmd = 'find ' . $context->getUnpackedFolder() . ' -type f -name \'*.php\' -exec php -l {} \; | grep -v "No syntax errors detected"';
        $res = shell_exec($cmd);

        if ($res === null) {
            return;
        }

        $errors = explode("\n", $res);
        foreach ($errors as $error) {
            $context->addViolation(sprintf('Found syntax error in file "%s"', $error));
        }
    }
}