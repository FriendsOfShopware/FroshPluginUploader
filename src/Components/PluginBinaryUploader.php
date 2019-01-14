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

        // Upload the binary
        $response = $this->client->post(sprintf('/plugins/%d/binaries', Util::getEnv('PLUGIN_ID')), [
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

        $responseJson = json_decode((string) $response->getBody(), true)[0];
        $responseJson['version'] = $xml->getVersion();
        $responseJson['changelogs'][0]['text'] = $xml->getNewestChangelog();
        $responseJson['changelogs'][1]['text'] = $xml->getNewestChangelog();
        $responseJson['ionCubeEncrypted'] = false;
        $responseJson['licenseCheckRequired'] = false;
        $responseJson['compatibleSoftwareVersions'] = iterator_to_array($this->getCompatibleShopwareVersions($xml->getMinVersion(), $xml->getMaxVersion()), false);

        // Patch the binary changelog and version
        $this->client->put(sprintf('/plugins/%d/binaries/%d', Util::getEnv('PLUGIN_ID'), $responseJson['id']), [
            'json' => $responseJson
        ]);

        // Trigger a review
//        $this->client->post(sprintf('/plugins/%d/reviews', Util::getEnv('PLUGIN_ID')), []);
    }

    private function validatePluginRequirements(string $pluginDirectory): void
    {
        if (!file_exists($pluginDirectory . '/plugin.xml')) {
            throw new \RuntimeException('Plugin must have a plugin.xml');
        }

        if (!Util::getEnv('PLUGIN_ID')) {
            throw new \RuntimeException('The enviroment variable $PLUGIN_ID is required');
        }
    }

    private function getCompatibleShopwareVersions(string $minVersion, ?string $maxVersion): \Generator
    {
        $versions = json_decode((string) $this->client->get('/pluginstatics/all')->getBody(), true)['softwareVersions'];

        foreach ($versions as $version) {
            if (version_compare($version['name'], $minVersion, '>=') && ($maxVersion === null || version_compare($version['name'], $maxVersion, '<='))) {
                $version['children'] = [];
                yield $version;
            }
        }
    }
}