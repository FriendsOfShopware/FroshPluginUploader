<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

use const JSON_THROW_ON_ERROR;

class General extends AbstractComponent
{
    /**
     * @var array
     */
    private $allData;

    public function getShopwareVersions(): array
    {
        return $this->getData()['softwareVersions'];
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
            $filter = json_encode([['property' => 'includeNonPublic', 'value' => 1]], JSON_THROW_ON_ERROR);
            $this->allData = json_decode((string) $this->client->get('/pluginstatics/all?filter=' . $filter)->getBody(), true, 512, JSON_THROW_ON_ERROR);
        }

        return $this->allData;
    }
}
