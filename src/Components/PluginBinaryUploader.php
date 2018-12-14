<?php

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;

class PluginBinaryUploader
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function upload(string $binaryPath, string $pluginDirectory): void
    {
        $this->validatePluginRequirements($pluginDirectory);

        $xml = new PluginXmlReader($pluginDirectory . '/plugin.xml');

        $response = $this->client->post(sprintf('/plugins/%d/binaries', Util::getEnv('PLUGIN_iD')), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($binaryPath, 'rb')
                ]
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(sprintf('Plugin binary upload failed with http status %d', $response->getStatusCode()));
        }


    }

    private function validatePluginRequirements(string $pluginDirectory): void
    {
        if (!file_exists($pluginDirectory . '/Resources/store/plugin.json')) {
            throw new \RuntimeException('Plugin store configuration does not exist under path Resources/store/plugin.json');
        }

        if (!file_exists($pluginDirectory . '/plugin.xml')) {
            throw new \RuntimeException('Plugin must have a plugin.xml');
        }

        if (!Util::getEnv('PLUGIN_ID')) {
            throw new \RuntimeException('The enviroment variable $PLUGIN_ID is required');
        }
    }
}