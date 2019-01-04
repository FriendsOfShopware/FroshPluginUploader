<?php

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginBinaryUploader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class UploadPluginCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure(): void
    {
        $this
            ->setName('frosh:plugin:upload')
            ->setDescription('Uploads a plugin binary to store.shopware.com')
            ->addArgument('zipPath', InputArgument::REQUIRED, 'Path to to the plugin binary')
            ->addArgument('pluginPath', InputArgument::REQUIRED, 'Path to to the plugin root directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->validateInput($input);

        $this->container->get(PluginBinaryUploader::class)->upload($input->getArgument('zipPath'), $input->getArgument('pluginPath'));

        $io = new SymfonyStyle($input, $output);
        $io->success('Plugin zip successfully uploaded');
    }

    private function validateInput(InputInterface $input): void
    {
        $zipPath = $input->getArgument('zipPath');
        $pluginPath = $input->getArgument('pluginPath');

        if (!file_exists($zipPath)) {
            throw new \RuntimeException(sprintf('Given path "%s" does not exists', $zipPath));
        }

        if (!file_exists($pluginPath)) {
            throw new \RuntimeException(sprintf('Given path "%s" does not exists', $pluginPath));
        }


    }
}