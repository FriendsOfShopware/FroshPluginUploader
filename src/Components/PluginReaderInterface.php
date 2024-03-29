<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

interface PluginReaderInterface
{
    public function all(): array;

    public function getVersion(): string;

    public function getNewestChangelogGerman(): string;

    public function getNewestChangelogEnglish(): string;

    public function getLabelGerman(): string;

    public function getLabelEnglish(): string;

    public function getDescriptionGerman(): string;

    public function getDescriptionEnglish(): string;

    public function getLicense(): string;

    public function getName(): string;

    public function getMinVersion(): string;

    public function getMaxVersion(): ?string;
}
