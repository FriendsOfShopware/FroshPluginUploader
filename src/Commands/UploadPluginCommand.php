<?php

namespace FroshPluginUploader\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadPluginCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('frosh:plugin:upload')
            ->setDescription('Uploads the plugin to store.shopware.com')
            ->addArgument('directory', InputArgument::REQUIRED, 'Path to your plugin directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {

    }
}