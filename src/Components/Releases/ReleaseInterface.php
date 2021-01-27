<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\Releases;

use FroshPluginUploader\Components\PluginInterface;

interface ReleaseInterface
{
    public function create(PluginInterface $plugin, string $zipPath): Release;
}
