<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\ResourcesDownloader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class DownloadPluginResourcesCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure(): void
    {
        $this
            ->setName('ext:download:resources')
            ->setAliases(['plugin:download:resources'])
            ->setDescription('Downloads the resources from account to given folder. Needed for plugin:upload')
            ->addArgument('name', InputArgument::REQUIRED, 'Technical plugin name')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to /Resources/store folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->container->get(ResourcesDownloader::class)->download($input->getArgument('name'), $input->getArgument('path'));

        $io = new SymfonyStyle($input, $output);
        $io->success('Downloaded store data to given folder');

        return 0;
    }
}
