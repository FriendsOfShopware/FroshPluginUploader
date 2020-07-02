<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginFinder;
use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Components\Util;
use FroshPluginUploader\Exception\PluginNotFoundInAccount;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ValidatePluginCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure(): void
    {
        $this
            ->setName('ext:validate')
            ->setAliases(['plugin:validate'])
            ->setDescription('Validate the plugin for the community store')
            ->addArgument('zipPath', InputArgument::REQUIRED, 'Path to to the plugin binary')
            ->addOption('create', null, InputOption::VALUE_NONE, 'Creates the plugin in store, when not present');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->validateInput($input);

        $zipPath = realpath($input->getArgument('zipPath'));
        $zip = new \ZipArchive();
        $zip->open($zipPath);
        $tmpFolder = Util::mkTempDir(basename($zipPath));
        $zip->extractTo($tmpFolder);

        $plugin = PluginFinder::findPluginByZipFile($tmpFolder);
        $plugin->getReader()->validate();

        $this->validateTechnicalName($plugin, $input->getOption('create'));

        $io = new SymfonyStyle($input, $output);
        $io->success('Has been successfully validated');

        return 0;
    }

    private function validateInput(InputInterface $input): void
    {
        $zipPath = $input->getArgument('zipPath');

        if (!file_exists($zipPath)) {
            throw new \RuntimeException(sprintf('Given path "%s" does not exists', $zipPath));
        }
    }

    private function validateTechnicalName(PluginInterface $plugin, bool $createIfNotExists = false): void
    {
        if (!isset($_SERVER['ACCOUNT_USER'])) {
            return;
        }

        $client = $this->container->get(Client::class);

        try {
            $client->Producer()->getPlugin($plugin->getName());
        } catch (PluginNotFoundInAccount $e) {
            if (!$createIfNotExists) {
                throw $e;
            }

            $client->Producer()->createPlugin($plugin->getName(), $plugin->getStoreType());
        }
    }
}
