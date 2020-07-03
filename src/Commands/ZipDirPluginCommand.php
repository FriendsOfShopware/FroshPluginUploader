<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginZip;
use FroshPluginUploader\Components\ZipStrategy\AbstractStrategy;
use FroshPluginUploader\Components\ZipStrategy\GitStrategy;
use FroshPluginUploader\Components\ZipStrategy\PlainStrategy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ZipDirPluginCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = realpath($input->getArgument('path'));

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('Folder by path %s does not exist', $input->getArgument('path')));
        }

        $this->container->get(PluginZip::class)->zip($path, $this->makeStrategy($input), $output);

        return 0;
    }

    protected function configure(): void
    {
        $this
            ->setName('ext:zip')
            ->setAliases(['plugin:zip:dir'])
            ->setDescription('Zips the given directory')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to to directory')
            ->addArgument('branch', InputArgument::OPTIONAL, 'Branch to checkout. Default newest version')
            ->addOption('strategy', null, InputOption::VALUE_OPTIONAL, 'Strategy to use git (git archive) or plain (copy folder)', 'git');
    }

    private function makeStrategy(InputInterface $input): AbstractStrategy
    {
        if ($input->getOption('strategy') === 'git') {
            return new GitStrategy($input->getArgument('branch'));
        }

        return new PlainStrategy();
    }
}
