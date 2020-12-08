<?php declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\PluginValidator\General\NotAllowedFilesInZipChecker;
use FroshPluginUploader\Components\ZipStrategy\AbstractStrategy;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginZip
{
    /**
     * @var AbstractStrategy
     */
    private $strategy;

    /**
     * @var PluginPrepare
     */
    private $pluginPrepare;

    private $defaultBlacklist = [
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

    public function __construct(AbstractStrategy $strategy, PluginPrepare $pluginPrepare)
    {
        $this->strategy = $strategy;
        $this->pluginPrepare = $pluginPrepare;
    }

    public function zip(string $directory, bool $scopeDependencies, OutputInterface $output): void
    {
        $io = new SymfonyStyle(new ArgvInput(), $output);

        $plugin = PluginFinder::findPluginByRootFolder($directory);

        $tmpDir = sys_get_temp_dir() . '/' . uniqid('uploaderPacking', true);
        $pluginTmpDir = $tmpDir . '/' . $plugin->getName();

        $this->exec(sprintf('mkdir -p %s', escapeshellarg($pluginTmpDir)));

        // Cleanup old releases
        $this->exec(sprintf('rm -rf %s', escapeshellarg($plugin->getName() . '-*.zip')));

        $version = $this->strategy->copyFolder($directory, $pluginTmpDir);
        $this->pluginPrepare->prepare($pluginTmpDir, $scopeDependencies, $output);

        if (file_exists($pluginTmpDir . '/.sw-zip-blacklist')) {
            $io->warning('Use of .sw-zip-blacklist is deprecated, use .gitattributes with export-ignore. It will be removed with 0.4.0');
            $blackList = file_get_contents($directory . '/.sw-zip-blacklist');
            $blackList = array_filter(explode("\n", $blackList));
            $this->defaultBlacklist = array_merge($this->defaultBlacklist, $blackList);
        }

        // Cleanup directory using blacklist
        foreach ($this->defaultBlacklist as $item) {
            $this->exec('rm -rf ' . escapeshellarg($pluginTmpDir . '/' . $item));
        }

        $this->removeBlacklistedStoreFiles($pluginTmpDir);

        // Clean branch name for filename

        if ($version) {
            $version = preg_replace('/[^a-z\.0-9]+/', '-', strtolower($version));
            $fileName = $plugin->getName() . '-' . $version . '.zip';
        } else {
            $fileName = $plugin->getName() . '.zip';
        }

        $this->exec(sprintf('cd %s; zip -r %s %s -x *.git*', escapeshellarg($tmpDir), escapeshellarg($fileName), escapeshellarg($plugin->getName())));

        $this->exec(sprintf('mv %s %s', escapeshellarg($tmpDir . '/' . $fileName), escapeshellarg(getcwd())));

        $this->exec('rm -rf ' . escapeshellarg($tmpDir));

        $io->success(sprintf('Created file %s', $fileName));
    }

    private function exec(string $command): void
    {
        exec($command, $output, $ret);

        // @codeCoverageIgnoreStart
        if ($ret !== 0) {
            throw new \RuntimeException(sprintf('Command "%s" failed with code %d', $command, $ret));
        }
        // @codeCoverageIgnoreEnd
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
