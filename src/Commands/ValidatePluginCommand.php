<?php
declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginFinder;
use FroshPluginUploader\Components\PluginInterface;
use FroshPluginUploader\Components\PluginValidator\ValidationInterface;
use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Exception\PluginNotFoundInAccount;
use FroshPluginUploader\Structs\Plugin;
use FroshPluginUploader\Structs\ViolationContext;
use FroshPluginUploader\Traits\ValidateZipTrait;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZipArchive;

/** @noinspection EfferentObjectCouplingInspection */
class ValidatePluginCommand extends Command
{
    use ValidateZipTrait;

    /**
     * @var ValidationInterface[]
     */
    private iterable $validators;

    private Client $client;

    public function __construct(iterable $validators, Client $client)
    {
        parent::__construct();
        $this->validators = $validators;
        $this->client = $client;
    }

    protected function configure(): void
    {
        $this
            ->setName('ext:validate')
            ->setAliases(['plugin:validate'])
            ->setDescription('Validate the plugin for the community store')
            ->addArgument('zipPath', InputArgument::REQUIRED, 'Path to to the plugin binary')
            ->addOption('create', null, InputOption::VALUE_NONE, 'Creates the plugin in store, when not present')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->validateInput($input);

        $zipPath = realpath($input->getArgument('zipPath'));
        $zip = new ZipArchive();
        $zip->open($zipPath);

        if (!mkdir($tmpFolder = sys_get_temp_dir() . '/' . uniqid('uploader', true)) && !is_dir($tmpFolder)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $tmpFolder));
        }

        $zip->extractTo($tmpFolder);

        $plugin = PluginFinder::findPluginByZipFile($tmpFolder);

        $storePlugin = $this->validateTechnicalName($plugin, $input->getOption('create'));

        $context = new ViolationContext($plugin, $zip, $tmpFolder, $storePlugin);
        foreach ($this->validators as $validator) {
            if ($validator->supports($context)) {
                $validator->validate($context);
            }
        }

        $io = new SymfonyStyle($input, $output);

        if ($context->hasViolations()) {
            $io->error('Found some issues in the plugin');

            $table = new Table($output);
            $table->setHeaders(['Message']);
            foreach ($context->getViolations() as $violation) {
                $table->addRow([$violation]);
            }
            $table->render();

            return 1;
        }

        $io->success('Has been successfully validated');

        return 0;
    }

    private function validateTechnicalName(PluginInterface $plugin, bool $createIfNotExists = false): ?Plugin
    {
        if (!isset($_SERVER['ACCOUNT_USER'])) {
            return null;
        }

        try {
            return $this->client->Producer()->getPlugin($plugin->getName());
        } catch (PluginNotFoundInAccount $e) {
            if (!$createIfNotExists) {
                throw $e;
            }

            return $this->client->Producer()->createPlugin($plugin->getName(), $plugin->getStoreType());
        }
    }
}
