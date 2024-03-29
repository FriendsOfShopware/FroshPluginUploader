<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\Generation\ShopwarePlatform;

use FroshPluginUploader\Exception\MissingChangelogException;
use RuntimeException;
use Symfony\Component\Finder\Finder;

class ChangelogReader
{
    private const FALLBACK_LOCALE = 'en-GB';

    private array $storage = [];

    public function __construct(string $pluginPath)
    {
        $finder = new Finder();
        $finder->files()->in($pluginPath)->name('CHANGELOG.md')->name('CHANGELOG_??-??.md')->depth(0);

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }

        $this->readFiles($files);
    }

    public function getChangelog(string $locale, string $version): string
    {
        $localeStorage = $this->storage[$locale] ?? null;

        if ($localeStorage === null) {
            if (!isset($this->storage[self::FALLBACK_LOCALE])) {
                throw new RuntimeException(sprintf('Changelog for locale "%s" does not exist', $locale));
            }

            $localeStorage = $this->storage[self::FALLBACK_LOCALE];
        }

        if (isset($localeStorage[$version])) {
            return $this->renderChangelogAsUl($localeStorage[$version]);
        }

        throw new RuntimeException(sprintf('Cannot find changelog for version "%s" with locale "%s"', $version, $locale));
    }

    private function renderChangelogAsUl(array $changelog): string
    {
        return '<ul><li>' . implode('</li><li>', $changelog) . '</li></ul>';
    }

    private function readFiles(array $files): void
    {
        if (empty($files)) {
            throw new MissingChangelogException('Changelogs are missing for plugin');
        }

        $parser = new ChangelogParser();

        foreach ($files as $file) {
            $locale = $this->getLocaleFromFileName($file);

            $this->storage[$locale] = $parser->parseChangelog($file);
        }
    }

    private function getLocaleFromFileName(string $fileName): string
    {
        $fileName = basename($fileName, '.md');

        if ($fileName === 'CHANGELOG') {
            return self::FALLBACK_LOCALE;
        }

        return mb_substr($fileName, mb_strpos($fileName, '_') + 1, 5);
    }
}
