<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

use FroshPluginUploader\Structs\Localizations;

class General extends AbstractComponent
{
    /**
     * @var array
     */
    private $allData;

    public function getCompatibleShopwareVersions(string $minVersion, ?string $maxVersion): \Generator
    {
        $versions = $this->getData()['softwareVersions'];

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

    public function getLocalizations(): array
    {
        return $this->getData()['localizations'];
    }

    public function all(): array
    {
        return $this->getData();
    }

    private function getData(): array
    {
        if ($this->allData === null) {
            $this->allData = json_decode((string) $this->client->get('/pluginstatics/all')->getBody(), true);
        }

        return $this->allData;
    }
}
