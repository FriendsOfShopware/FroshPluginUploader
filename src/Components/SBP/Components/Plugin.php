<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

use FroshPluginUploader\Structs\Binary;
use FroshPluginUploader\Structs\Picture;

class Plugin extends AbstractComponent
{
    public function get(int $pluginId): \FroshPluginUploader\Structs\Plugin
    {
        return \FroshPluginUploader\Structs\Plugin::map(json_decode((string) $this->client->get(sprintf('/plugins/%d', $pluginId))->getBody()));
    }

    public function put(int $pluginId, object $data): void
    {
        $this->client->put(sprintf('/plugins/%d', $pluginId), ['json' => $data]);
    }

    /**
     * @param int $pluginId
     *
     * @return Picture[]
     */
    public function getImages(int $pluginId): array
    {
        return Picture::mapList(json_decode((string) $this->client->get(sprintf('/plugins/%d/pictures', $pluginId))->getBody()));
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
     *
     * @return Binary[]
     */
    public function getAvailableBinaries(int $pluginId): array
    {
        return Binary::mapList(json_decode((string) $this->client->get(sprintf('/plugins/%d/binaries', $pluginId))->getBody()));
    }

    public function createBinaryFile(string $binaryPath, int $pluginId): Binary
    {
        $response = $this->client->post(sprintf('/plugins/%d/binaries', $pluginId), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($binaryPath, 'rb'),
                ],
            ],
        ]);

        return Binary::map(json_decode((string) $response->getBody())[0]);
    }

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

    public function updateBinary($binary, int $pluginId): void
    {
        $this->client->put(sprintf('/plugins/%d/binaries/%d', $pluginId, $binary->id), [
            'json' => $binary,
        ]);
    }

    public function triggerCodeReview(int $pluginId): void
    {
        $this->client->post(sprintf('/plugins/%d/reviews', $pluginId));
    }

    public function getVersion(array $binaries, string $version): ?Binary
    {
        $versionArray = array_values(array_filter($binaries, function (Binary $binary) use ($version) {
            return $binary->version === $version;
        }));

        return $versionArray[0] ?? null;
    }

    public function hasVersion(array $binaries, string $version): bool
    {
        return (bool) $this->getVersion($binaries, $version);
    }
}
