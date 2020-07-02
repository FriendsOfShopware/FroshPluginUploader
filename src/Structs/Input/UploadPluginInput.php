<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs\Input;

use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Structs\Plugin;

class UploadPluginInput
{
    /**
     * @var string
     */
    private $zipPath;

    /**
     * @var bool
     */
    private $skipCodeReview;

    /**
     * @var bool
     */
    private $skipWaitingForCodeReview;

    /**
     * @var PluginInterface
     */
    private $plugin;

    /**
     * @var Plugin
     */
    private $storePlugin;

    public function __construct(string $zipPath, PluginInterface $plugin, Plugin $storePlugin, bool $skipCodeReview, bool $skipWaitingForCodeReview)
    {
        $this->zipPath = $zipPath;
        $this->skipCodeReview = $skipCodeReview;
        $this->skipWaitingForCodeReview = $skipWaitingForCodeReview;
        $this->plugin = $plugin;
        $this->storePlugin = $storePlugin;
    }

    public function getZipPath(): string
    {
        return $this->zipPath;
    }

    public function isSkipCodeReview(): bool
    {
        return $this->skipCodeReview;
    }

    public function isSkipWaitingForCodeReview(): bool
    {
        return $this->skipWaitingForCodeReview;
    }

    public function getPlugin(): PluginInterface
    {
        return $this->plugin;
    }

    public function getStorePlugin(): Plugin
    {
        return $this->storePlugin;
    }
}