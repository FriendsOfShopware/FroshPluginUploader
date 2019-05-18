<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Structs\Binary;

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

    public function upload(string $binaryPath, PluginInterface $plugin, bool $skipCodeReviewResult = false)
    {
        $pluginId = (int) Util::getEnv('PLUGIN_ID');

        $plugin->getReader()->validate();

        $binaries = $this->client->Plugins()->getAvailableBinaries($pluginId);

        if (!$this->client->Plugins()->hasVersion($binaries, $plugin->getReader()->getVersion())) {
            $binary = $this->client->Plugins()->createBinaryFile($binaryPath, $pluginId);
        } else {
            $binary = $this->updateBinary($binaries, $plugin->getReader()->getVersion(), $binaryPath, $pluginId);
        }

        $binary->version = $plugin->getReader()->getVersion();
        $binary->changelogs[0]->text = $plugin->getReader()->getNewestChangelogGerman();
        $binary->changelogs[1]->text = $plugin->getReader()->getNewestChangelogEnglish();
        $binary->ionCubeEncrypted = false;
        $binary->licenseCheckRequired = false;
        $binary->compatibleSoftwareVersions = iterator_to_array($this->client->General()->getCompatibleShopwareVersions($plugin->getReader()->getMinVersion(), $plugin->getReader()->getMaxVersion()), false);

        // Patch the binary changelog and version
        $this->client->Plugins()->updateBinary($binary, $pluginId);

        $currentReviews = count($this->client->Plugins()->getCodeReviewResults($pluginId, $binary->id));

        // Trigger a review
        $this->client->Plugins()->triggerCodeReview($pluginId);

        if ($skipCodeReviewResult) {
            return true;
        }

        return $this->waitForResult($currentReviews, $pluginId, $binary->id);
    }

    /**
     * @param array  $binaries
     * @param string $version
     * @param string $binaryPath
     * @param int    $pluginId
     *
     * @return Binary
     */
    private function updateBinary(array $binaries, string $version, string $binaryPath, int $pluginId)
    {
        $binary = $this->client->Plugins()->getVersion($binaries, $version);

        $this->client->Plugins()->updateBinaryFile($binary->id, $binaryPath, $pluginId);

        $binaries = $this->client->Plugins()->getAvailableBinaries($pluginId);

        return $this->client->Plugins()->getVersion($binaries, $version);
    }

    private function waitForResult(int $counter, int $pluginId, int $binaryId)
    {
        $tries = 0;

        sleep(5);

        while (true) {
            $results = $this->client->Plugins()->getCodeReviewResults($pluginId, $binaryId);

            if ($counter !== count($results)) {
                $result = $results[count($results) - 1];

                if ($result->type->id === 3) {
                    return true;
                }

                return $results[count($results) - 1]->message;
            }

            sleep(5);

            ++$tries;

            if ($tries === 15) {
                return false;
            }
        }
    }
}
