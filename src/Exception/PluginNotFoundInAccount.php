<?php
declare(strict_types=1);

namespace FroshPluginUploader\Exception;

class PluginNotFoundInAccount extends \RuntimeException
{
    public function __construct(string $pluginName)
    {
        parent::__construct(sprintf('Cannot find plugin "%s" in your Shopware Account', $pluginName));
    }
}
