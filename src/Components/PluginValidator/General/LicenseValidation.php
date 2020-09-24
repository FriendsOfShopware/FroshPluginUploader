<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\General;

use Composer\Spdx\SpdxLicenses;
use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Structs\ViolationContext;

class LicenseValidation implements ValidationInterface
{
    public function supports(ViolationContext $context): bool
    {
        return true;
    }

    public function validate(ViolationContext $context): void
    {
        $spdxLicences = new SpdxLicenses();
        if ($spdxLicences->validate([$context->getPlugin()->getReader()->getLicense()]) === false) {
            $context->addViolation('The license must comply with a valid open-source identifier. https://spdx.org/licenses');
        }

        if ($context->getStorePlugin() && $context->getStorePlugin()->license->name === 'proprietary' && $context->getPlugin()->getReader()->getLicense() !== 'proprietary') {
            $context->addViolation('The account plugin license does not match the license from the zipped plugin');
        }
    }
}
