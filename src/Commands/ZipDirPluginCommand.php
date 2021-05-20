<?php
declare(strict_types=1);

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

class ZipDirPluginCommand extends Command
{
    private PluginZip $pluginZip;

    public function __construct(PluginZip $pluginZip)
    {
        parent::__construct();
        $this->pluginZip = $pluginZip;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = realpath($input->getArgument('path'));

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('Folder by path %s does not exist', $input->getArgument('path')));
        }

        $this->pluginZip->zip($path, (bool) $input->getOption('scope'), $output, $this->makeStrategy($input));

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
            ->addOption('strategy', null, InputOption::VALUE_OPTIONAL, 'Strategy to use git (git archive) or plain (copy folder)', 'git')
            ->addOption('scope', null, InputOption::VALUE_OPTIONAL, 'Will attempt to scope plugin dependencies into a distinct namespace to avoid conflict. A config file is recommended on plugin level.', false)
        ;
    }

    private function makeStrategy(InputInterface $input): AbstractStrategy
    {
        if ($input->getOption('strategy') === 'git') {
            return new GitStrategy($input->getArgument('branch'));
        }

        return new PlainStrategy();
    }
}
