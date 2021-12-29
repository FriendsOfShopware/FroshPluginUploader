<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\Releases\Gitlab;

use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POSTFIELDS;
use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Components\Releases\Release;
use FroshPluginUploader\Components\Releases\ReleaseInterface;
use Gitlab\Client;

class Gitlab implements ReleaseInterface
{
    private Client $client;

    public function create(PluginInterface $plugin, string $zipPath): Release
    {
        $this->client = new Client();

        if (isset($_SERVER['CI_SERVER_URL'])) {
            $this->client->setUrl($_SERVER['CI_SERVER_URL']);
        }

        $this->client->authenticate($_SERVER['CI_JOB_TOKEN'], Client::AUTH_HTTP_TOKEN);

        $releases = $this->client->repositories()->releases($_SERVER['CI_PROJECT_ID']);

        foreach ($releases as $release) {
            if ($release['tag_name'] === $plugin->getReader()->getVersion()) {
                $this->updateAndUploadZip($plugin, $zipPath);

                return new Release('');
            }
        }

        $this->client->repositories()->createRelease($_SERVER['CI_PROJECT_ID'], $plugin->getReader()->getVersion(), '');

        $this->updateAndUploadZip($plugin, $zipPath);

        return new Release('');
    }

    private function updateAndUploadZip(PluginInterface $plugin, string $zipPath): void
    {
        $this->client->repositories()->updateRelease(
            $_SERVER['CI_PROJECT_ID'],
            $plugin->getReader()->getVersion(),
            '## Changelog' . "\n" . $plugin->getReader()->getNewestChangelogEnglish()
        );

        $ch = curl_init(sprintf('https://gitlab.com/api/v4/projects/%s/packages/generic/%s/%s/%s', $_SERVER['CI_PROJECT_ID'], $plugin->getName(), $plugin->getReader()->getVersion(), $plugin->getName() . '.zip'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'PRIVATE-TOKEN: ' . $_SERVER['CI_JOB_TOKEN'],
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($zipPath));

        var_dump(curl_exec($ch));
    }
}
