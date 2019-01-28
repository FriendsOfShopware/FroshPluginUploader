<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginReader;
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

        $reader = new PluginReader(Util::getPluginPath($tmpFolder));
        $reader->validate();

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
}
