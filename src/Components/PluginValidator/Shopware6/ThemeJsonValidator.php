<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\PluginValidator\Shopware6;

use function file_exists;
use FroshPluginUploader\Components\Generation\ShopwareApp\App;
use FroshPluginUploader\Components\Generation\ShopwarePlatform\Plugin;
use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Structs\ViolationContext;
use function json_decode;
use const JSON_THROW_ON_ERROR;
use function sprintf;

class ThemeJsonValidator implements ValidationInterface
{
    public function supports(ViolationContext $context): bool
    {
        return $context->getPlugin() instanceof Plugin || $context->getPlugin() instanceof App;
    }

    public function validate(ViolationContext $context): void
    {
        $themeJsonPath = sprintf('%s/%s/src/Resources/theme.json', $context->getUnpackedFolder(), $context->getPlugin()->getName());

        if (is_file($themeJsonPath) === false) {
            return;
        }

        $themeJson = json_decode(file_get_contents($themeJsonPath), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($themeJson['previewMedia'])) {
            $context->addViolation('Required field "previewMedia" in theme.json is not in');

            return;
        }

        $imagePath = sprintf('%s/%s/src/Resources/%s', $context->getUnpackedFolder(), $context->getPlugin()->getName(), $themeJson['previewMedia']);

        if (!file_exists($imagePath)) {
            $context->addViolation(sprintf('Theme preview image file is expected to be placed at "%s", but not found there.', $imagePath));
        }
    }
}
