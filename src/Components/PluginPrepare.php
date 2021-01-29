<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use JakubOnderka\PhpVarDumpCheck\Manager;
use JakubOnderka\PhpVarDumpCheck\Output;
use JakubOnderka\PhpVarDumpCheck\Settings;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginPrepare
{
    public function prepare(string $directory, bool $scopeDependencies, OutputInterface $output): void
    {
        $composerJson = $directory . '/composer.json';
        $composerJsonBackup = $composerJson . '.bak';
        $composerLock = $directory . '/composer.lock';

        $io = new SymfonyStyle(new ArgvInput(), $output);

        if (file_exists($composerJson)) {
            copy($composerJson, $composerJsonBackup);
            $this->filterShopwareDependencies($composerJson);

            // Install composer dependencies
            if ($this->needComposerToRun($composerJson)) {
                // delete composer.lock file before calling `composer install` - see #112 for details
                if (file_exists($composerLock)) {
                    unlink($composerLock);
                }

                $this->exec('composer install --ignore-platform-reqs --no-dev -n -d ' . escapeshellarg($directory));

                // TODO: Maybe refactor this into own service
                if ($scopeDependencies) {
                    $plugin = PluginFinder::findPluginByRootFolder($directory);
                    $this->scopeDependencies($io, $plugin, $directory);
                }
                $this->exec('composer dump -o -d ' . escapeshellarg($directory));
            }

            rename($composerJsonBackup, $composerJson);
        }

        $settings = new Settings();
        $settings->excluded = [$directory . '/vendor'];
        $settings->paths = [$directory];

        $manager = new Manager();
        $buffer = new BufferedWriter();
        if (!$manager->check($settings, new Output($buffer))) {
            // @codeCoverageIgnoreStart
            $io->error($buffer->getOutput());
            exit(254);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Remove Shopware base packages from composer.json
     * so they aren't bundled with the plugin.
     */
    private function filterShopwareDependencies(string $composerJsonPath): void
    {
        $json = json_decode(file_get_contents($composerJsonPath), true);

        $keys = ['shopware/platform', 'shopware/core', 'shopware/storefront', 'shopware/administration', 'composer/installers'];
        foreach ($keys as $key) {
            if (isset($json['require'][$key])) {
                unset($json['require'][$key]);
            }
        }

        // Add these packages as provided by the plugin
        $json['provide'] = array_combine($keys, array_fill(0, count($keys), '*'));

        file_put_contents(
            $composerJsonPath,
            json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    private function needComposerToRun(string $composerJsonPath): bool
    {
        $json = json_decode(file_get_contents($composerJsonPath), true);

        // Plugin does not require anything
        if (empty($json['require'])) {
            return false;
        }

        return true;
    }

    /**
     * @codeCoverageIgnore
     */
    private function scopeDependencies(
        SymfonyStyle $io,
        Generation\ShopwarePlatform\Plugin $plugin,
        string $directory
    ): void {
        try {
            $this->exec('command -v php-scoper');
        } catch (\RuntimeException $e) {
            $io->warning('Could not find php-scoper executable in PATH');

            return;
        }
        $io->writeln('Scoping plugin dependencies into ' . $plugin->getName() . '\\ namespace.');
        $this->exec(
            'php-scoper add-prefix -n -o ' . escapeshellarg($directory)
            . ' -d ' . escapeshellarg($directory)
            . ' -p ' . $plugin->getName()
        );
        $io->writeln('');
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
}
