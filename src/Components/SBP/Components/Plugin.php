<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

class Plugin extends AbstractComponent
{
    public function get(int $pluginId): array
    {
        return json_decode((string) $this->client->get(sprintf('/plugins/%d', $pluginId))->getBody(), true);
    }

    public function put(int $pluginId, array $data): void
    {
        $this->client->put(sprintf('/plugins/%d', $pluginId), ['json' => $data]);
    }

    public function getImages(int $pluginId)
    {
        return json_decode((string) $this->client->get(sprintf('/plugins/%d/pictures', $pluginId))->getBody(), true);
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
}
