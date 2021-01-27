<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\Releases\Github;

use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Components\Releases\Release;
use FroshPluginUploader\Components\Releases\ReleaseInterface;
use Github\Client;

class Github implements ReleaseInterface
{
    private Client $client;
    private string $user;
    private string $repo;

    public function create(PluginInterface $plugin, string $zipPath): Release
    {
        $this->client = new Client();
        $this->client->authenticate($_SERVER['GITHUB_TOKEN'], Client::AUTH_ACCESS_TOKEN);

        if (!isset($_SERVER['GITHUB_REPOSITORY'])) {
            throw new \RuntimeException('$GITHUB_REPOSITORY is missing with content org/repo');
        }

        [$this->user, $this->repo] = explode('/', $_SERVER['GITHUB_REPOSITORY']);

        $alreadyExistingTags = $this->client->repo()->releases()->all($this->user, $this->repo, ['per_page' => 100]);

        foreach ($alreadyExistingTags as $tag) {
            if ($tag['tag_name'] === $plugin->getReader()->getVersion()) {
                $this->updateAndUploadZip($tag['id'], $plugin, $zipPath);

                return new Release($tag['html_url']);
            }
        }

        $tag = $this->client->repo()->releases()->create($this->user, $this->repo, [
            'tag_name' => $plugin->getReader()->getVersion(),
        ]);

        $this->updateAndUploadZip($tag['id'], $plugin, $zipPath);

        return new Release($tag['html_url']);
    }

    private function updateAndUploadZip(int $id, PluginInterface $plugin, string $zipPath): void
    {
        $this->client->repo()->releases()->edit($this->user, $this->repo, $id, [
            'body' => '## Changelog' . "\n" . $plugin->getReader()->getNewestChangelogEnglish(),
        ]);

        $ourAssetName = $plugin->getName() . '.zip';

        $assets = $this->client->repo()->releases()->assets()->all($this->user, $this->repo, $id);

        // Remove already existing asset
        foreach ($assets as $asset) {
            if ($asset['name'] == $ourAssetName) {
                $this->client->repo()->releases()->assets()->remove($this->user, $this->repo, $asset['id']);
            }
        }

        $this->client->repo()->releases()->assets()->create($this->user, $this->repo, $id, $ourAssetName, 'application/zip', file_get_contents($zipPath));
    }
}
