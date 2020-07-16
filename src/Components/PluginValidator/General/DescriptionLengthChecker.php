<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\General;

use FroshPluginUploader\Components\Generation\ShopwareApp\App;
use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Structs\ViolationContext;

class DescriptionLengthChecker implements ValidationInterface
{
    public function supports(ViolationContext $context): bool
    {
        return !($context->getPlugin() instanceof App);
    }

    public function validate(ViolationContext $context): void
    {
        $pluginReader = $context->getPlugin()->getReader();
        $violationMsg = 'The %s description with length of %s should have a length from 150 up to 185 characters.';

        $lengthDescriptionGerman = strlen($pluginReader->getDescriptionGerman());
        if ($lengthDescriptionGerman < 150 || $lengthDescriptionGerman > 185) {
            $context->addViolation(sprintf($violationMsg, 'German', $lengthDescriptionGerman));
        }

        $lengthDescriptionEnglish = strlen($pluginReader->getDescriptionEnglish());
        if ($lengthDescriptionEnglish < 150 || $lengthDescriptionEnglish > 185) {
            $context->addViolation(sprintf($violationMsg, 'English', $lengthDescriptionEnglish));
        }
    }
}
