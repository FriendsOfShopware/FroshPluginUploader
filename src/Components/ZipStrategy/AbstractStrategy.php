<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\ZipStrategy;

use FroshPluginUploader\Traits\ExecTrait;

abstract class AbstractStrategy
{
    use ExecTrait;

    abstract public function copyFolder(string $sourceFolder, string $targetFolder): ?string;
}
