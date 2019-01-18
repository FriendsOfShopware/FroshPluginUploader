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
        $pluginId = (int) Util::getEnv('PLUGIN_ID');
        $xml = new PluginReader($pluginDirectory);
        $xml->validate();

        $binaries = $this->getAvailableBinaries($pluginId);

        if (!$this->hasVersion($binaries, $xml->getVersion())) {
            $binary = $this->createBinary($binaryPath, $pluginId);
        } else {
            $binary = $this->updateBinary($binaries, $xml->getVersion(), $binaryPath, $pluginId);
        }

        $binary['version'] = $xml->getVersion();
        $binary['changelogs'][0]['text'] = $xml->getNewestChangelogGerman();
        $binary['changelogs'][1]['text'] = $xml->getNewestChangelogEnglish();
        $binary['ionCubeEncrypted'] = false;
        $binary['licenseCheckRequired'] = false;
        $binary['compatibleSoftwareVersions'] = iterator_to_array($this->getCompatibleShopwareVersions($xml->getMinVersion(), $xml->getMaxVersion()), false);

        // Patch the binary changelog and version
        $this->client->put(sprintf('/plugins/%d/binaries/%d', $pluginId, $binary['id']), [
            'json' => $binary,
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

    private function getAvailableBinaries(int $pluginId): array
    {
        return json_decode((string) $this->client->get(sprintf('/plugins/%d/binaries', $pluginId))->getBody(), true);
    }

    private function getVersion(array $binaries, string $version): ?array
    {
        $versionArray = array_values(array_filter($binaries, function($binary) use($version) {
            return $binary['version'] === $version;
        }));

        return $versionArray[0] ?? null;
    }

    private function hasVersion(array $binaries, string $version): bool
    {
        return (bool) $this->getVersion($binaries, $version);
    }

    /**
     * @param string $binaryPath
     * @param int $pluginId
     * @return array
     */
    private function createBinary(string $binaryPath, int $pluginId): array
    {
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

        return json_decode((string) $response->getBody(), true)[0];
    }

    private function updateBinary(array $binaries, string $version, string $binaryPath, int $pluginId): array
    {
        $binary = $this->getVersion($binaries, $version);

        // Update the binary
        $this->client->post(sprintf('/plugins/%d/binaries/%d/file', $pluginId, $binary['id']), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($binaryPath, 'rb'),
                ],
            ],
        ]);

        $binaries = $this->getAvailableBinaries($pluginId);

        return $this->getVersion($binaries, $version);
    }
}
