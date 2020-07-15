<?php declare(strict_types=1);


namespace FroshPluginUploader\Structs;

use FroshPluginUploader\Components\PluginInterface;

class ViolationContext
{
    /**
     * @var PluginInterface
     */
    private $plugin;

    /**
     * @var \ZipArchive
     */
    private $zipArchive;

    /**
     * @var string
     */
    private $unpackedFolder;

    /**
     * @var ?Plugin
     */
    private $storePlugin;

    /**
     * @var array
     */
    private $violations = [];

    public function __construct(PluginInterface $plugin, \ZipArchive $zipArchive, string $unpackedFolder, ?Plugin $storePlugin = null)
    {
        $this->plugin = $plugin;
        $this->zipArchive = $zipArchive;
        $this->unpackedFolder = $unpackedFolder;
        $this->storePlugin = $storePlugin;
    }

    public function addViolation(string $violation): void
    {
        $this->violations[] = $violation;
    }

    public function hasViolations(): bool
    {
        return count($this->violations) > 0;
    }

    public function getViolations(): array
    {
        return $this->violations;
    }

    public function getPlugin(): PluginInterface
    {
        return $this->plugin;
    }

    public function getZipArchive(): \ZipArchive
    {
        return $this->zipArchive;
    }

    public function getUnpackedFolder(): string
    {
        return $this->unpackedFolder;
    }

    public function getStorePlugin(): ?Plugin
    {
        return $this->storePlugin;
    }
}