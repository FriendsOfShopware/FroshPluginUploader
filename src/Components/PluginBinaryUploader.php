<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Structs\LatestBinary;

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

        $binaries = $this->client->Plugins()->getAvailableBinaries($pluginId);

        if (!$this->client->Plugins()->hasVersion($binaries, $xml->getVersion())) {
            $binary = $this->client->Plugins()->createBinaryFile($binaryPath, $pluginId);
        } else {
            $binary = $this->updateBinary($binaries, $xml->getVersion(), $binaryPath, $pluginId);
        }

        $binary->version = $xml->getVersion();
        $binary->changelogs[0]->text = $xml->getNewestChangelogGerman();
        $binary->changelogs[1]->text = $xml->getNewestChangelogEnglish();
        $binary->ionCubeEncrypted = false;
        $binary->licenseCheckRequired = false;
        $binary->compatibleSoftwareVersions = iterator_to_array($this->client->General()->getCompatibleShopwareVersions($xml->getMinVersion(), $xml->getMaxVersion()), false);

        // Patch the binary changelog and version
        $this->client->Plugins()->updateBinary($binary, $pluginId);

        // Trigger a review
        $this->client->Plugins()->triggerCodeReview($pluginId);
    }

    /**
     * @param array $binaries
     * @param string $version
     * @param string $binaryPath
     * @param int $pluginId
     * @return LatestBinary
     */
    private function updateBinary(array $binaries, string $version, string $binaryPath, int $pluginId)
    {
        $binary = $this->client->Plugins()->getVersion($binaries, $version);

        $this->client->Plugins()->updateBinaryFile($binary->id, $binaryPath, $pluginId);

        $binaries = $this->client->Plugins()->getAvailableBinaries($pluginId);

        return $this->client->Plugins()->getVersion($binaries, $version);
    }
}
