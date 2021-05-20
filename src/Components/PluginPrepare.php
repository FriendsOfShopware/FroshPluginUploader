<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Traits\ExecTrait;
use JakubOnderka\PhpVarDumpCheck\Manager;
use JakubOnderka\PhpVarDumpCheck\Output;
use JakubOnderka\PhpVarDumpCheck\Settings;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginPrepare
{
    use ExecTrait;

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

                $this->exec('composer install --no-dev -n -d ' . escapeshellarg($directory));

                // TODO: Maybe refactor this into own service
                if ($scopeDependencies) {
                    $plugin = PluginFinder::findPluginByRootFolder($directory);
                    $this->scopeDependencies($io, $plugin, $directory);
                }
                $this->exec('composer dump -o -d ' . escapeshellarg($directory));

                // The InstalledVersions from composer 2 introduces issues when they exists multiple times. This patches it out
                $installedVersions = $directory . '/vendor/composer/InstalledVersions.php';

                $stripInstalledVersionRegex = '/\s*\'Composer\\\\\\\\InstalledVersions.*,$/m';

                $autoloadClassMap = $directory . '/vendor/composer/autoload_classmap.php';
                $autoloadStatic = $directory . '/vendor/composer/autoload_static.php';
                $installedJson = $directory . '/vendor/composer/installed.json';
                $installedPhp = $directory . '/vendor/composer/installed.php';

                if (is_file($installedVersions)) {
                    unlink($installedVersions);

                    if (is_file($autoloadClassMap)) {
                        \file_put_contents($autoloadClassMap, preg_replace($stripInstalledVersionRegex, '', file_get_contents($autoloadClassMap)));
                    }

                    if (is_file($autoloadStatic)) {
                        \file_put_contents($autoloadStatic, preg_replace($stripInstalledVersionRegex, '', file_get_contents($autoloadStatic)));
                    }

                    if (is_file($installedPhp)) {
                        unlink($installedPhp);
                    }

                    if (is_file($installedJson)) {
                        unlink($installedJson);
                    }
                }
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
            json_encode($json, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE)
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
        PluginInterface $plugin,
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
}
