<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

class Producer extends AbstractComponent
{
    public function getProducer($cache = false): array
    {
        $producer['cache'] = "var/cache/producer.json";
        if ($cache || !file_exists($producer['cache'])) {
            $this->updateProducerCache($producer['cache']);
        }
        return json_decode(file_get_contents($producer['cache']), true)[0];
    }

    public function getPlugins($cache = false): array
    {
        $plugins['cache'] = "var/cache/plugins.json";

        $producer = $this->getProducer($cache);

        $query = [
            'limit' => 100,
            'offset' => 0,
            'orderBy' => 'creationDate',
            'orderSequence' => 'desc',
            'producerId' => $producer['id'],
        ];

        if ($cache || !file_exists($plugins['cache'])) {
            $this->updatePluginsCache($plugins['cache'], $query);
        }

        return json_decode(file_get_contents($plugins['cache']), true);
    }

    public function updateProducerCache($producerCache)
    {
        file_put_contents($producerCache, $this->client->get('/producers')->getBody());
    }

    public function updatePluginsCache($pluginCache, $query)
    {
        file_put_contents($pluginCache, $this->client->get('/plugins?' . http_build_query($query))->getBody());
    }
}
