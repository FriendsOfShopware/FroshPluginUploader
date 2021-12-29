<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

use FroshPluginUploader\Exception\PluginNotFoundInAccount;
use FroshPluginUploader\Structs\Plugin;
use FroshPluginUploader\Structs\Producer as ProducerStruct;
use const JSON_THROW_ON_ERROR;

class Producer extends AbstractComponent
{
    public function getProducer(): ProducerStruct
    {
        return ProducerStruct::map(json_decode((string) $this->client->get('/producers?companyId=' . $this->client->getUserId())->getBody(), false, 512, JSON_THROW_ON_ERROR)[0]);
    }

    /**
     * @return Plugin[]
     */
    public function getPlugins(?string $search = null): array
    {
        $query = [
            'limit' => 100,
            'offset' => 0,
            'orderBy' => 'creationDate',
            'orderSequence' => 'desc',
            'producerId' => $this->client->getProducer()->id,
        ];

        if ($search) {
            $query['search'] = $search;
        }

        return Plugin::mapList(json_decode((string) $this->client->get('/plugins', ['query' => $query])->getBody(), false, 512, JSON_THROW_ON_ERROR));
    }

    public function getPlugin(string $name): Plugin
    {
        $plugins = $this->getPlugins($name);

        foreach ($plugins as $plugin) {
            if (mb_strtolower($name) === mb_strtolower($plugin->name)) {
                return $this->client->Plugins()->get($plugin->id);
            }
        }

        throw new PluginNotFoundInAccount($name);
    }

    public function createPlugin(string $name, string $generation): Plugin
    {
        $plugin = (string) $this->client->post('/plugins', [
            'json' => [
                'generation' => [
                    'name' => $generation,
                ],
                'producerId' => $this->client->getProducer()->id,
            ],
        ])->getBody();

        $createdPlugin = Plugin::map(json_decode($plugin, false, 512, JSON_THROW_ON_ERROR));
        $createdPlugin->name = $name;

        return $this->client->Plugins()->put($createdPlugin->id, $createdPlugin);
    }
}
