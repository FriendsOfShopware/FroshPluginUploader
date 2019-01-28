<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

use FroshPluginUploader\Structs\Plugin;

class Producer extends AbstractComponent
{
    public function getProducer(): \FroshPluginUploader\Structs\Producer
    {
        return \FroshPluginUploader\Structs\Producer::map(json_decode((string) $this->client->get('/producers')->getBody())[0]);
    }

    /**
     * @param int $producerId
     *
     * @return Plugin[]
     */
    public function getPlugins(int $producerId): array
    {
        $query = [
            'limit' => 100,
            'offset' => 0,
            'orderBy' => 'creationDate',
            'orderSequence' => 'desc',
            'producerId' => $producerId,
        ];

        return Plugin::mapList(json_decode((string) $this->client->get('/plugins?' . http_build_query($query))->getBody()));
    }
}
