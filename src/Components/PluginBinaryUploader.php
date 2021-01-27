<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Structs\Binary;
use FroshPluginUploader\Structs\Input\UploadPluginInput;
use FroshPluginUploader\Structs\Input\UploadPluginResult;

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

    public function upload(UploadPluginInput $input): UploadPluginResult
    {
        $binaries = $this->client->Plugins()->getAvailableBinaries($input->getStorePlugin()->id);

        if (!$this->client->Plugins()->hasVersion($binaries, $input->getPlugin()->getReader()->getVersion())) {
            $binary = $this->client->Plugins()->createBinaryFile($input->getZipPath(), $input->getStorePlugin()->id);
        } else {
            $binary = $this->updateBinary($binaries, $input->getPlugin()->getReader()->getVersion(), $input->getZipPath(), $input->getStorePlugin()->id);
        }

        $binary->version = $input->getPlugin()->getReader()->getVersion();
        $binary->changelogs[0]->text = $input->getPlugin()->getReader()->getNewestChangelogGerman();
        $binary->changelogs[1]->text = $input->getPlugin()->getReader()->getNewestChangelogEnglish();
        $binary->ionCubeEncrypted = false;
        $binary->licenseCheckRequired = false;
        $binary->compatibleSoftwareVersions = $input->getPlugin()->getCompatibleVersions($this->client->General()->getShopwareVersions());

        // Patch the binary changelog and version
        $this->client->Plugins()->updateBinary($binary, $input->getStorePlugin()->id);

        $currentReviews = count($this->client->Plugins()->getCodeReviewResults($input->getStorePlugin()->id, $binary->id));

        if ($input->isSkipCodeReview()) {
            return new UploadPluginResult(true, false);
        }

        // Trigger a review
        $this->client->Plugins()->triggerCodeReview($input->getStorePlugin()->id);

        if ($input->isSkipWaitingForCodeReview()) {
            return new UploadPluginResult(true, false);
        }

        return $this->waitForResult($currentReviews, $input->getStorePlugin()->id, $binary->id);
    }

    /**
     * @return Binary
     */
    private function updateBinary(array $binaries, string $version, string $binaryPath, int $pluginId)
    {
        $binary = $this->client->Plugins()->getVersion($binaries, $version);

        $this->client->Plugins()->updateBinaryFile($binary->id, $binaryPath, $pluginId);

        $binaries = $this->client->Plugins()->getAvailableBinaries($pluginId);

        return $this->client->Plugins()->getVersion($binaries, $version);
    }

    private function waitForResult(int $counter, int $pluginId, int $binaryId): UploadPluginResult
    {
        $tries = 0;

        while (true) {
            $results = $this->client->Plugins()->getCodeReviewResults($pluginId, $binaryId);

            if ($counter !== count($results)) {
                $result = $results[count($results) - 1];

                // Still pending
                // @codeCoverageIgnoreStart
                if ($result->type->id === 4) {
                    sleep(5);
                    ++$tries;
                    continue;
                }
                // @codeCoverageIgnoreEnd

                return CodeReviewFormatter::format($result);
            }

            // @codeCoverageIgnoreStart
            sleep(5);

            ++$tries;

            if ($tries === 200) {
                return new UploadPluginResult(true, false, 'Code-Review check took to long');
            }
            // @codeCoverageIgnoreIgnoreEnd
        }
    }
}
