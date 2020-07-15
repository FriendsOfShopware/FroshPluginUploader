<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\Shopware6;

use FroshPluginUploader\Components\Generation\ShopwarePlatform\Plugin;
use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Structs\ViolationContext;

class ComposerJsonChecker implements ValidationInterface
{
    private const REQUIRED_KEYS_COMPOSER_JSON = [
        'name',
        'type',
        'description',
        'license',
        'version',
        'authors',
        'require',
    ];

    private const REQUIRED_KEYS_EXTRA = [
        'shopware-plugin-class',
        'label',
        'description',
        'manufacturerLink',
        'supportLink',
    ];

    public function supports(ViolationContext $context): bool
    {
        return $context->getPlugin() instanceof Plugin;
    }

    public function validate(ViolationContext $context): void
    {
        $this->validateComposerJson($context);
        $this->doesPluginBootstrapExists($context);
        $this->doesAtLeastOneAutoLoadExists($context);
    }

    private function validateComposerJson(ViolationContext $context): void
    {
        $json = $context->getPlugin()->getReader()->all();

        // Validate keys
        foreach (self::REQUIRED_KEYS_COMPOSER_JSON as $requiredKey) {
            if (!isset($json[$requiredKey])) {
                $context->addViolation(sprintf('"%s" is not defined in composer.json', $requiredKey));
            }
        }

        // Validate extra keys
        foreach (self::REQUIRED_KEYS_EXTRA as $requiredKey) {
            if (!isset($json['extra'][$requiredKey])) {
                $context->addViolation(sprintf('"%s" is not defined in composer.json "extra" section', $requiredKey));
            }
        }

        // Call the changelog methods, it will throw a exception if they are missing
        $context->getPlugin()->getReader()->getNewestChangelogGerman();
        $context->getPlugin()->getReader()->getNewestChangelogEnglish();
    }

    private function doesPluginBootstrapExists(ViolationContext $context): void
    {
        $composerJson = $context->getPlugin()->getReader()->all();
        $shopwarePluginClass = $composerJson['extra']['shopware-plugin-class'];

        $autoload = $composerJson['autoload'];

        $psr4 = $autoload['psr-4'] ?? [];
        $psr0 = $autoload['psr-0'] ?? [];

        $matchingPath = null;
        foreach ($psr4 as $namespace => $path) {
            $expectedPluginClass = $namespace . $context->getPlugin()->getName();
            if ($shopwarePluginClass === $expectedPluginClass) {
                $matchingPath = $path;
                break;
            }
        }

        foreach ($psr0 as $namespace => $path) {
            $expectedPluginClass = $namespace . $context->getPlugin()->getName();
            if ($shopwarePluginClass === $expectedPluginClass) {
                $matchingPath = $path;
                break;
            }
        }

        if (null === $matchingPath) {
            $context->addViolation(sprintf('Plugin bootstrap file "%s" cannot be found in the defined psr-0 or psr-4 autoload paths', $shopwarePluginClass));
        }

        $bootstrapFilePath = $context->getPlugin()->getName() . '/' .  $matchingPath . $context->getPlugin()->getName() . '.php';
        $stat = $context->getZipArchive()->statName($bootstrapFilePath);
        if ($stat === false) {
            $context->addViolation(sprintf('Cannot find plugin bootstrap file in zip at path "%s"', $bootstrapFilePath));
        }
    }

    private function doesAtLeastOneAutoLoadExists(ViolationContext $context): void
    {
        $composerJson = $context->getPlugin()->getReader()->all();
        $psr4 = $composerJson['autoload']['psr-4'] ?? [];
        $psr0 = $composerJson['autoload']['psr-0'] ?? [];

        if (count($psr0) === 0 && count($psr4) === 0) {
            $context->addViolation('At least one of the properties psr-0 or psr-4 are required in the composer.json');
        }
    }
}