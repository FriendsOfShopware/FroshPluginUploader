<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginZip;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ZipDirPluginCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $path = realpath($input->getArgument('gitPath'));

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('Folder by path %s does not exist', $input->getArgument('gitPath')));
        }

        $zipPath = $this->container->get(PluginZip::class)->zip($path, $input->getArgument('branch'));

        $io = new SymfonyStyle($input, $output);

        $io->success(sprintf('Created file %s', basename($zipPath)));
    }

    protected function configure()
    {
        $this
            ->setName('plugin:zip:dir')
            ->setDescription('Zips the given directory')
            ->addArgument('gitPath', InputArgument::REQUIRED, 'Path to to git directory')
            ->addArgument('branch', InputArgument::OPTIONAL, 'Branch to checkout. Default newest version');
    }
}
