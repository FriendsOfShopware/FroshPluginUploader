<?php
declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginFinder;
use FroshPluginUploader\Components\PluginUpdater;
use FroshPluginUploader\Components\SBP\Client;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @noinspection PhpUnused */
class UpdatePluginCommand extends Command
{
    private Client $client;
    private PluginUpdater $pluginUpdater;

    public function __construct(Client $client, PluginUpdater $pluginUpdater)
    {
        parent::__construct();
        $this->client = $client;
        $this->pluginUpdater = $pluginUpdater;
    }

    protected function configure(): void
    {
        $this
            ->setName('ext:update')
            ->setAliases(['plugin:update'])
            ->setDescription('Synchronize the plugin information to the Shopware store, for plugins it should be located at Resources/store, for apps the store folder in the plugin root folder is used.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to plugin folder')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = realpath($input->getArgument('path'));

        if (!file_exists($path)) {
            throw new RuntimeException(sprintf('Folder by path %s does not exist', $input->getArgument('path')));
        }

        $plugin = PluginFinder::findPluginByRootFolder($path);
        $storePlugin = $this->client->Producer()->getPlugin($plugin->getName());

        $this->pluginUpdater->sync($plugin, $storePlugin);

        $io = new SymfonyStyle($input, $output);
        $io->success('Store folder has been applied to plugin page');

        return 0;
    }
}
