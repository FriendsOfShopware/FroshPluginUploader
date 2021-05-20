<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use JakubOnderka\PhpVarDumpCheck\Writer\Writer;

class BufferedWriter implements Writer
{
    private array $output = [];

    public function write($string): void
    {
        $this->output[] = $string;
    }

    public function getOutput(): string
    {
        return implode('', $this->output);
    }
}
