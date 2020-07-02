<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

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
            $this->allData = json_decode((string) $this->client->get('/pluginstatics/all')->getBody(), true);
        }

        return $this->allData;
    }
}
