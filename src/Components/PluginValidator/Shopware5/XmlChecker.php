<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\Shopware5;

use FroshPluginUploader\Components\Generation\Shopware5\Plugin;
use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Structs\ViolationContext;

class XmlChecker implements ValidationInterface
{
    private const REQUIRED_KEYS = [
        'copyright',
        'license',
        'author',
        'compatibility',
        'changelog',
        'label',
        'version',
        'description',
    ];

    public function supports(ViolationContext $context): bool
    {
        return $context->getPlugin() instanceof Plugin;
    }

    public function validate(ViolationContext $context): void
    {
        $this->hasPluginXml($context);
        $this->hasPluginIcon($context);

        $this->checkRequiredXmlFields($context);
    }

    private function hasPluginXml(ViolationContext $context): void
    {
        if (!file_exists($context->getUnpackedFolder() . '/' . $context->getPlugin()->getName() .  '/plugin.xml')) {
            $context->addViolation('Plugin requires an plugin.xml');
        }
    }

    private function hasPluginIcon(ViolationContext $context): void
    {
        if (!file_exists($context->getUnpackedFolder() . '/' . $context->getPlugin()->getName() . '/plugin.png')) {
            $context->addViolation('Plugin requires an plugin.png');
        }
    }

    private function checkRequiredXmlFields(ViolationContext $context): void
    {
        $xml = $context->getPlugin()->getReader()->all();

        foreach (self::REQUIRED_KEYS as $requiredKey) {
            if (!isset($xml[$requiredKey])) {
                $context->addViolation(sprintf('%s is not defined in plugin.xml', ucfirst($requiredKey)));
            }
        }
    }
}