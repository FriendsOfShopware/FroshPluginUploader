<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

class Producer extends AbstractComponent
{
    public function getProducer(): array
    {
        return json_decode((string) $this->client->get('/producers')->getBody(), true)[0];
    }

    public function getPlugins(int $producerId): array
    {
        $query = [
            'limit' => 100,
            'offset' => 0,
            'orderBy' => 'creationDate',
            'orderSequence' => 'desc',
            'producerId' => $producerId,
        ];

        return json_decode((string) $this->client->get('/plugins?' . http_build_query($query))->getBody(), true);
    }
}
