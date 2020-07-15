<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\General;

use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Structs\ViolationContext;

class ZipFolderMatchesTechnicalPluginName implements ValidationInterface
{
    public function supports(ViolationContext $context): bool
    {
        return true;
    }

    public function validate(ViolationContext $context): void
    {
        $firstFolder = $context->getZipArchive()->statIndex(0)['name'];

        if ($firstFolder === $context->getPlugin()->getName() . '/') {
            return;
        }

        $context->addViolation(sprintf('The plugin needs to be zipped with an folder "%s". You can use unzip -l Plugin.zip to show the content', $context->getPlugin()->getName()));
    }
}