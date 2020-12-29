<?php

namespace FroshPluginUploader\Components;

use JakubOnderka\PhpVarDumpCheck\Writer\Writer;

class BufferedWriter implements Writer
{
    private array $output = [];

    public function write($string)
    {
        $this->output[] = $string;
    }

    public function getOutput(): string
    {
        return implode('', $this->output);
    }
}