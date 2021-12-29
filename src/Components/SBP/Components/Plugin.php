<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

use FroshPluginUploader\Structs\Binary;
use FroshPluginUploader\Structs\CodeReview\CodeReview;
use FroshPluginUploader\Structs\Image;
use FroshPluginUploader\Structs\Picture;
use FroshPluginUploader\Structs\Plugin as StorePlugin;
use const JSON_THROW_ON_ERROR;

class Plugin extends AbstractComponent
{
    public function get(int $pluginId): StorePlugin
    {
        return StorePlugin::map(json_decode((string) $this->client->get(sprintf('/plugins/%d', $pluginId))->getBody(), false, 512, JSON_THROW_ON_ERROR));
    }

    public function put(int $pluginId, object $data): StorePlugin
    {
        return StorePlugin::map(json_decode((string) $this->client->put(sprintf('/plugins/%d', $pluginId), ['json' => $data])->getBody(), false, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @return Picture[]
     */
    public function getImages(int $pluginId): array
    {
        return Picture::mapList(json_decode((string) $this->client->get(sprintf('/plugins/%d/pictures', $pluginId))->getBody(), false, 512, JSON_THROW_ON_ERROR));
    }

    public function deleteImage(int $pluginId, int $imageId): void
    {
        $this->client->delete(sprintf('/plugins/%d/pictures/%d', $pluginId, $imageId));
    }

    public function addImage(int $pluginId, string $path): Image
    {
        $json = (string) $this->client->post(sprintf('/plugins/%d/pictures', $pluginId), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($path, 'r'),
                ],
            ],
        ])->getBody();

        return Image::mapList(json_decode($json, false, 512, JSON_THROW_ON_ERROR))[0];
    }

    public function updateImage(int $pluginId, Image $image): void
    {
        $this->client->put(sprintf('/plugins/%d/pictures/%d', $pluginId, $image->id), [
            'json' => $image,
        ]);
    }

    public function addIcon(int $pluginId, string $path): void
    {
        $this->client->post(sprintf('/plugins/%d/icon', $pluginId), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($path, 'r'),
                ],
            ],
        ]);
    }

    /**
     * @return Binary[]
     */
    public function getAvailableBinaries(int $pluginId): array
    {
        return Binary::mapList(json_decode((string) $this->client->get(sprintf('/plugins/%d/binaries', $pluginId))->getBody(), false, 512, JSON_THROW_ON_ERROR));
    }

    public function createBinaryFile(string $binaryPath, int $pluginId): Binary
    {
        $response = $this->client->post(sprintf('/plugins/%d/binaries', $pluginId), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($binaryPath, 'r'),
                ],
            ],
        ]);

        return Binary::map(json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR)[0]);
    }

    public function updateBinaryFile(int $binaryId, string $binaryPath, int $pluginId): void
    {
        $this->client->post(sprintf('/plugins/%d/binaries/%d/file', $pluginId, $binaryId), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($binaryPath, 'r'),
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
        $versionArray = array_values(array_filter($binaries, static function (Binary $binary) use ($version) {
            return $binary->version === $version;
        }));

        return $versionArray[0] ?? null;
    }

    public function hasVersion(array $binaries, string $version): bool
    {
        return (bool) $this->getVersion($binaries, $version);
    }

    /**
     * @return CodeReview[]
     */
    public function getCodeReviewResults(int $pluginId, int $binaryId): array
    {
        return CodeReview::mapList(json_decode((string) $this->client->get(sprintf('/plugins/%d/binaries/%d/checkresults', $pluginId, $binaryId))->getBody(), false, 512, JSON_THROW_ON_ERROR));
    }
}
