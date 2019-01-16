<?php declare(strict_types=1);

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
        $pluginId = Util::getEnv('PLUGIN_ID');
        $xml = new PluginReader($pluginDirectory);
        $xml->validate();

        // Upload the binary
        $response = $this->client->post(sprintf('/plugins/%d/binaries', $pluginId), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($binaryPath, 'rb'),
                ],
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(sprintf('Plugin binary upload failed with http status %d', $response->getStatusCode()));
        }

        $responseJson = json_decode((string) $response->getBody(), true)[0];
        $responseJson['version'] = $xml->getVersion();
        $responseJson['changelogs'][0]['text'] = $xml->getNewestChangelogEnglish();
        $responseJson['changelogs'][1]['text'] = $xml->getNewestChangelogGerman();
        $responseJson['ionCubeEncrypted'] = false;
        $responseJson['licenseCheckRequired'] = false;
        $responseJson['compatibleSoftwareVersions'] = iterator_to_array($this->getCompatibleShopwareVersions($xml->getMinVersion(), $xml->getMaxVersion()), false);

        // Patch the binary changelog and version
        $this->client->put(sprintf('/plugins/%d/binaries/%d', $pluginId, $responseJson['id']), [
            'json' => $responseJson,
        ]);

        // Trigger a review
        $this->client->post(sprintf('/plugins/%d/reviews', $pluginId), []);
    }

    private function getCompatibleShopwareVersions(string $minVersion, ?string $maxVersion): \Generator
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
