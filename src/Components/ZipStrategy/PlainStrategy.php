<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\ZipStrategy;

class PlainStrategy extends AbstractStrategy
{
    public function copyFolder(string $sourceFolder, string $targetFolder): ?string
    {
        $this->exec(sprintf('cp -a -R %s/. %s/', escapeshellarg($sourceFolder), escapeshellarg($targetFolder)));

        return null;
    }
}
