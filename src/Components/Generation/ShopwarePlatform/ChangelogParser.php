<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\ShopwarePlatform;

use FroshPluginUploader\Exception\ChangelogInvalidException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ChangelogParser
{
    public function parseChangelog(string $path): array
    {
        $releases = [];
        $currentRelease = null;

        foreach ($this->parse($path) as $line) {
            switch ($line[0]) {
                case '#':
                    $currentRelease = $this->parseTitle($line);
                    break;
                case '-':
                case '*':
                    if (!$currentRelease) {
                        throw new ChangelogInvalidException(sprintf('Changelog with path "%s" is invalid', $path));
                    }

                    $releases[$currentRelease][] = $this->parseItem($line);
                    break;
            }
        }

        return $releases;
    }

    private function parse(string $path): \Generator
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException(null, 0, null, $path);
        }

        $file = fopen($path, 'r');

        while ($line = fgets($file)) {
            yield $line;
        }
        fclose($file);
    }

    private function parseTitle($line): string
    {
        return mb_strtolower(trim(mb_substr($line, 1)));
    }

    private function parseItem($line): string
    {
        return trim(mb_substr($line, 1));
    }
}
