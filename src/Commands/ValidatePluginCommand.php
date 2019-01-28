<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginReader;
use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Components\Util;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ValidatePluginCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('plugin:validate')
            ->setDescription('Validate the plugin for the community store')
            ->addArgument('zipPath', InputArgument::REQUIRED, 'Path to to the plugin binary');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validateInput($input);

        $zipPath = realpath($input->getArgument('zipPath'));
        $zip = new \ZipArchive();
        $zip->open($zipPath);
        $tmpFolder = Util::mkTempDir(basename($zipPath));
        $zip->extractTo($tmpFolder);

        $reader = new PluginReader($this->getPluginPath($tmpFolder));
        $reader->validate();

        $this->validateTechnicalName($tmpFolder);

        $io = new SymfonyStyle($input, $output);
        $io->success('Plugin has been successfully validated');
    }

    private function validateInput(InputInterface $input): void
    {
        $zipPath = $input->getArgument('zipPath');

        if (!file_exists($zipPath)) {
            throw new \RuntimeException(sprintf('Given path "%s" does not exists', $zipPath));
        }
    }

    private function getPluginPath(string $tmpFolder): string
    {
        $dir = current(array_filter(scandir($tmpFolder, SCANDIR_SORT_NONE), function ($value) {
            return $value[0] !== '.';
        }));

        $pluginXmlPath = $tmpFolder . '/' . $dir . '/plugin.xml';

        if (!file_exists($pluginXmlPath)) {
            throw new \RuntimeException('Cannot find a plugin.xml in zip file');
        }

        return $tmpFolder . '/' . $dir;
    }

    private function validateTechnicalName(string $tmpFolder)
    {
        $pluginId = (int) Util::getEnv('PLUGIN_ID');

        if (empty($pluginId)) {
            return;
        }

        $plugin = $this->container->get(Client::class)->Plugins()->get($pluginId);
        $zipPluginName = Util::getPluginName($tmpFolder);

        if ($plugin->moduleKey !== $zipPluginName) {
            throw new \RuntimeException(sprintf('Plugin name in zip does not match account plugin technical name, Account: %s, Zip: %s', $plugin->moduleKey, $zipPluginName));
        }
    }
}
