<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\ShopwareAppSystem;

use FroshPluginUploader\Components\Generation\ShopwareApp\App;
use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Structs\ViolationContext;

class ManifestChecker implements ValidationInterface
{
    private const REQUIRED_KEYS = [
        'label',
        'version',
        'license',
    ];

    public function supports(ViolationContext $context): bool
    {
        return $context->getPlugin() instanceof App;
    }

    public function validate(ViolationContext $context): void
    {
        $config = $context->getPlugin()->getReader()->all();

        // Validate keys
        foreach (self::REQUIRED_KEYS as $requiredKey) {
            if (!isset($config[$requiredKey])) {
                $context->addViolation(sprintf('"%s" is not defined in manifest.xml', $requiredKey));
            }
        }

        // Call the changelog methods, it will throw a exception if they are missing
        $context->getPlugin()->getReader()->getNewestChangelogGerman();
        $context->getPlugin()->getReader()->getNewestChangelogEnglish();
    }
}