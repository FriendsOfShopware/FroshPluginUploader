<?php

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginBinaryUploader;
use FroshPluginUploader\Components\Util;
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
            ->setName('plugin:upload')
            ->setDescription('Uploads a plugin binary to store.shopware.com')
            ->addArgument('zipPath', InputArgument::REQUIRED, 'Path to to the plugin binary');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->validateInput($input);

        $zipPath = $input->getArgument('zipPath');
        $zip = new \ZipArchive();
        $zip->open($zipPath);
        $tmpFolder = Util::mkTempDir(basename($zipPath));

        $zip->extractTo($tmpFolder);

        $this->container->get(PluginBinaryUploader::class)->upload($input->getArgument('zipPath'), $this->getPluginPath($tmpFolder));

        $io = new SymfonyStyle($input, $output);
        $io->success('Plugin zip successfully uploaded');
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
        $dir = current(array_filter(scandir($tmpFolder, SCANDIR_SORT_NONE), function($value) {
            return $value[0] !== '.';
        }));

        $pluginXmlPath = $tmpFolder . '/' . $dir . '/plugin.xml';

        if (!file_exists($pluginXmlPath)) {
            throw new \RuntimeException('Cannot find a plugin.xml in zip file');
        }

        return $tmpFolder . '/' . $dir;
    }
}