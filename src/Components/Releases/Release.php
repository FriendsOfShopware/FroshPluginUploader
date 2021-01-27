<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\Releases;

class Release
{
    private string $releaseUrl;

    public function __construct(string $releaseUrl = '')
    {
        $this->releaseUrl = $releaseUrl;
    }

    public function getReleaseUrl(): string
    {
        return $this->releaseUrl;
    }
}
