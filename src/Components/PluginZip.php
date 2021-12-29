<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\PluginValidator\General\NotAllowedFilesInZipChecker;
use FroshPluginUploader\Components\ZipStrategy\AbstractStrategy;
use FroshPluginUploader\Traits\ExecTrait;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginZip
{
    use ExecTrait;

    private PluginPrepare $pluginPrepare;

    private const DEFAULT_BLACKLIST = [
        '.travis.yml',
        '.gitlab-ci.yml',
        'build.sh',
        '.editorconfig',
        '.php_cs.dist',
        '.php_cs.cache',
        'ISSUE_TEMPLATE.md',
        '.sw-zip-blacklist',
        'tests',
        'Resources/store',
        'src/Resources/store',
        '.github',
    ];

    public function __construct(PluginPrepare $pluginPrepare)
    {
        $this->pluginPrepare = $pluginPrepare;
    }

    public function zip(string $directory, bool $scopeDependencies, OutputInterface $output, AbstractStrategy $strategy): void
    {
        $io = new SymfonyStyle(new ArgvInput(), $output);

        $plugin = PluginFinder::findPluginByRootFolder($directory);

        $tmpDir = sys_get_temp_dir() . '/' . uniqid('uploaderPacking', true);
        $pluginTmpDir = $tmpDir . '/' . $plugin->getName();

        $this->exec(sprintf('mkdir -p %s', escapeshellarg($pluginTmpDir)));

        // Cleanup old releases
        $this->exec(sprintf('rm -rf %s', escapeshellarg($plugin->getName() . '-*.zip')));

        $version = $strategy->copyFolder($directory, $pluginTmpDir);
        $this->pluginPrepare->prepare($pluginTmpDir, $scopeDependencies, $output);

        $defaultBlacklist = self::DEFAULT_BLACKLIST;

        if (file_exists($pluginTmpDir . '/.sw-zip-blacklist')) {
            $io->warning('Use of .sw-zip-blacklist is deprecated, use .gitattributes with export-ignore. It will be removed with 0.4.0');
            $blackList = file_get_contents($directory . '/.sw-zip-blacklist');
            $blackList = array_filter(explode("\n", $blackList));
            $defaultBlacklist = array_merge($defaultBlacklist, $blackList);
        }

        // Cleanup directory using blacklist
        foreach ($defaultBlacklist as $item) {
            $this->exec('rm -rf ' . escapeshellarg($pluginTmpDir . '/' . $item));
        }

        $this->removeBlacklistedStoreFiles($pluginTmpDir);

        // Clean branch name for filename

        if ($version) {
            $version = preg_replace('/[^a-z.0-9]+/', '-', mb_strtolower($version));
            $fileName = $plugin->getName() . '-' . $version . '.zip';
        } else {
            $fileName = $plugin->getName() . '.zip';
        }

        $this->exec(sprintf('cd %s; zip -r %s %s -x *.git*', escapeshellarg($tmpDir), escapeshellarg($fileName), escapeshellarg($plugin->getName())));

        $this->exec(sprintf('mv %s %s', escapeshellarg($tmpDir . '/' . $fileName), escapeshellarg(getcwd())));

        $this->exec('rm -rf ' . escapeshellarg($tmpDir));

        $io->success(sprintf('Created file %s', $fileName));
    }

    private function removeBlacklistedStoreFiles(string $pluginTmpDir): void
    {
        // Remove not allowed store file extensions
        foreach (NotAllowedFilesInZipChecker::NOT_ALLOWED_EXTENSIONS as $item) {
            $this->exec('(find ' . escapeshellarg($pluginTmpDir . '/') . ' -iname \'*' . escapeshellarg($item) . '\') | xargs rm -rf');
        }

        // Remove not allowed store files
        foreach (NotAllowedFilesInZipChecker::NOT_ALLOWED_FILES as $item) {
            $this->exec('(find ' . escapeshellarg($pluginTmpDir . '/') . ' -iname \'' . escapeshellarg($item) . '\') | xargs rm -rf');
        }
    }
}
