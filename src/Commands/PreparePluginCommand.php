<?php
declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginPrepare;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PreparePluginCommand extends Command
{
    private PluginPrepare $pluginPrepare;

    public function __construct(PluginPrepare $pluginPrepare)
    {
        parent::__construct();
        $this->pluginPrepare = $pluginPrepare;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = realpath($input->getArgument('path'));

        if (!file_exists($path)) {
            throw new RuntimeException(sprintf('Folder by path %s does not exist', $input->getArgument('path')));
        }

        $this->pluginPrepare->prepare($path, (bool) $input->getOption('scope'), $output);

        return 0;
    }

    protected function configure(): void
    {
        $this
            ->setName('ext:prepare')
            ->setAliases(['plugin:install:dependencies'])
            ->setDescription('Install all composer plugin dependencies')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to to directory')
            ->addOption('scope', null, InputOption::VALUE_OPTIONAL, 'Will attempt to scope plugin dependencies into a distinct namespace to avoid conflict. A config file is recommended on plugin level.', false)
        ;
    }
}
