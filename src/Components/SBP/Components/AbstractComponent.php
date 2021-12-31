<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP\Components;

use FroshPluginUploader\Components\SBP\Client;

class AbstractComponent
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
