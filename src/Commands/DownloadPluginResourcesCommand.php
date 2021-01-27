<?php
declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\ResourcesDownloader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DownloadPluginResourcesCommand extends Command
{
    private ResourcesDownloader $resourcesDownloader;

    public function __construct(ResourcesDownloader $resourcesDownloader)
    {
        parent::__construct();
        $this->resourcesDownloader = $resourcesDownloader;
    }

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
        $this->resourcesDownloader->download($input->getArgument('name'), $input->getArgument('path'));

        $io = new SymfonyStyle($input, $output);
        $io->success('Downloaded store data to given folder');

        return 0;
    }
}
