<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

use FroshPluginUploader\Structs\LatestBinary;
use FroshPluginUploader\Structs\Picture;

class Plugin extends AbstractComponent
{
    /**
     * @param int $pluginId
     * @return \FroshPluginUploader\Structs\Plugin
     */
    public function get(int $pluginId)
    {
        return json_decode((string) $this->client->get(sprintf('/plugins/%d', $pluginId))->getBody());
    }

    public function put(int $pluginId, object $data): void
    {
        $this->client->put(sprintf('/plugins/%d', $pluginId), ['json' => $data]);
    }

    /**
     * @param int $pluginId
     * @return Picture[]
     */
    public function getImages(int $pluginId)
    {
        return json_decode((string) $this->client->get(sprintf('/plugins/%d/pictures', $pluginId))->getBody());
    }

    public function deleteImage(int $pluginId, int $imageId): void
    {
        $this->client->delete(sprintf('/plugins/%d/pictures/%d', $pluginId, $imageId));
    }

    public function addImage(int $pluginId, string $path): void
    {
        $this->client->post(sprintf('/plugins/%d/pictures', $pluginId), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($path, 'rb'),
                ],
            ],
        ]);
    }

    /**
     * @param int $pluginId
     * @return LatestBinary[]
     */
    public function getAvailableBinaries(int $pluginId)
    {
        return json_decode((string) $this->client->get(sprintf('/plugins/%d/binaries', $pluginId))->getBody());
    }

    /**
     * @param string $binaryPath
     * @param int $pluginId
     * @return LatestBinary
     */
    public function createBinaryFile(string $binaryPath, int $pluginId)
    {
        $response = $this->client->post(sprintf('/plugins/%d/binaries', $pluginId), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($binaryPath, 'rb'),
                ],
            ],
        ]);

        return json_decode((string) $response->getBody())[0];
    }

    /**
     * @param int $binaryId
     * @param string $binaryPath
     * @param int $pluginId
     */
    public function updateBinaryFile(int $binaryId, string $binaryPath, int $pluginId): void
    {
        $this->client->post(sprintf('/plugins/%d/binaries/%d/file', $pluginId, $binaryId), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($binaryPath, 'rb'),
                ],
            ],
        ]);
    }

    /**
     * @param LatestBinary $binary
     * @param int          $pluginId
     */
    public function updateBinary($binary, int $pluginId): void
    {
        $this->client->put(sprintf('/plugins/%d/binaries/%d', $pluginId, $binary->id), [
            'json' => $binary
        ]);
    }

    public function triggerCodeReview(int $pluginId): void
    {
        $this->client->post(sprintf('/plugins/%d/reviews', $pluginId));
    }

    /**
     * @param LatestBinary[] $binaries
     * @param string $version
     * @return LatestBinary|null
     */
    public function getVersion(array $binaries, string $version)
    {
        $versionArray = array_values(array_filter($binaries, function ($binary) use ($version) {
            return $binary->version === $version;
        }));

        return $versionArray[0] ?? null;
    }

    public function hasVersion(array $binaries, string $version): bool
    {
        return (bool) $this->getVersion($binaries, $version);
    }
}
