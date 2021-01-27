<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\ZipStrategy;

abstract class AbstractStrategy
{
    abstract public function copyFolder(string $sourceFolder, string $targetFolder): ?string;

    protected function exec(string $command): ?array
    {
        exec($command, $output, $ret);

        if ($ret !== 0) {
            throw new \RuntimeException(sprintf('Command "%s" failed with code %d', $command, $ret));
        }

        return $output;
    }
}
