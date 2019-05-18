<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

use FroshPluginUploader\Structs\Localizations;

class General extends AbstractComponent
{
    public function getCompatibleShopwareVersions(string $minVersion, ?string $maxVersion): \Generator
    {
        $versions = json_decode((string) $this->client->get('/pluginstatics/all')->getBody(), true)['softwareVersions'];

        foreach ($versions as $version) {
            if (!$version['selectable']) {
                continue;
            }

            if (version_compare($version['name'], $minVersion, '>=') && ($maxVersion === null || version_compare($version['name'], $maxVersion, '<='))) {
                $version['children'] = [];
                yield $version;
            }
        }
    }

    /**
     * @return Localizations[]
     */
    public function getLocalizations(): array
    {
        return Localizations::mapList(json_decode((string) $this->client->get('/pluginstatics/all')->getBody())->localizations);
    }
}
