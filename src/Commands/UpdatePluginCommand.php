<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginFinder;
use FroshPluginUploader\Components\PluginUpdater;
use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Components\Util;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class UpdatePluginCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('plugin:update')
            ->setDescription('Synchronize the Resources/store to the account')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to plugin folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = realpath($input->getArgument('path'));

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('Folder by path %s does not exist', $input->getArgument('path')));
        }

        $plugin = PluginFinder::findPluginByRootFolder($path);
        $storePlugin = $this->container->get(Client::class)->Producer()->getPlugin($plugin->getName());

        $this->container->get(PluginUpdater::class)->sync($plugin, $storePlugin);

        $io = new SymfonyStyle($input, $output);
        $io->success('Store folder has been applied to plugin page');

        return 0;
    }
}
