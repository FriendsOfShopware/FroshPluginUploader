<?php


namespace FroshPluginUploader\Components\SBP\Components;


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
}