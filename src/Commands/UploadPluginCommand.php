<?php
declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginBinaryUploader;
use FroshPluginUploader\Components\PluginFinder;
use FroshPluginUploader\Components\ReleaseFactory;
use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Structs\Input\UploadPluginInput;
use FroshPluginUploader\Traits\ValidateZipTrait;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZipArchive;

/** @noinspection PhpUnused */
class UploadPluginCommand extends Command
{
    use ValidateZipTrait;

    private Client $client;
    private PluginBinaryUploader $pluginBinaryUploader;
    private ReleaseFactory $releaseFactory;

    public function __construct(Client $client, PluginBinaryUploader $pluginBinaryUploader, ReleaseFactory $releaseFactory)
    {
        parent::__construct();
        $this->client = $client;
        $this->pluginBinaryUploader = $pluginBinaryUploader;
        $this->releaseFactory = $releaseFactory;
    }

    protected function configure(): void
    {
        $this
            ->setName('ext:upload')
            ->setAliases(['plugin:upload'])
            ->setDescription('Uploads a plugin binary to store.shopware.com')
            ->addArgument('zipPath', InputArgument::REQUIRED, 'Path to to the plugin binary')
            ->addOption('skipCodeReview', null, InputOption::VALUE_NONE, 'Don\'t trigger code review')
            ->addOption('skipCodeReviewResult', 's', InputOption::VALUE_NONE, 'Dont wait for code-review result')
            ->addOption('createRelease', null, InputOption::VALUE_NONE, 'Create a Github Release')
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
        $storePlugin = $this->client->Producer()->getPlugin($plugin->getName());

        $pluginInput = new UploadPluginInput(
            $zipPath,
            $plugin,
            $storePlugin,
            $input->getOption('skipCodeReview'),
            $input->getOption('skipCodeReviewResult')
        );

        $result = $this->pluginBinaryUploader->upload($pluginInput);

        $io = new SymfonyStyle($input, $output);

        if ($result->isPassed() && !$result->hasWarnings()) {
            $io->success('Zip successfully uploaded');
        } elseif ($result->isPassed()) {
            $io->success('Uploaded but with Warnings');
            $io->warning($result->getMessage());
        } else {
            $io->error($result->getMessage());
        }

        if ($result->isPassed() && $input->getOption('createRelease')) {
            $release = $this->releaseFactory->get()->create($plugin, $zipPath);
            $io->success('Created a new Release ' . $release->getReleaseUrl());
        }

        return $result->isPassed() ? 0 : -1;
    }
}
