<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\ZipStrategy;

class GitStrategy extends AbstractStrategy
{
    /**
     * @var string|null
     */
    private $branch;

    public function __construct(?string $branch)
    {
        $this->branch = $branch;
    }

    public function copyFolder(string $sourceFolder, string $targetFolder): ?string
    {
        $branch = $this->branch ?? $this->getFallbackBranch($sourceFolder);

        $this->exec(sprintf('git -C %s archive %s | tar -x -C %s', escapeshellarg($sourceFolder), escapeshellarg($branch), escapeshellarg($targetFolder)));

        return $branch;
    }

    private function getFallbackBranch(string $directory)
    {
        $output = $this->exec(sprintf('git -C %s tag --sort=-creatordate | head -1', escapeshellarg($directory)));

        return $output[0] ?? 'master';
    }
}
