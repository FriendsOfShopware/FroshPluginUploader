<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\PluginBinaryUploader;
use FroshPluginUploader\Components\Util;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addArgument('zipPath', InputArgument::REQUIRED, 'Path to to the plugin binary')
            ->addOption('skipCodeReviewResult', 's', InputOption::VALUE_NONE, 'Dont wait for code-review result');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if (!Util::getEnv('PLUGIN_ID')) {
            throw new \RuntimeException('The enviroment variable $PLUGIN_ID is required');
        }

        $this->validateInput($input);

        $zipPath = realpath($input->getArgument('zipPath'));
        $zip = new \ZipArchive();
        $zip->open($zipPath);
        $tmpFolder = Util::mkTempDir(basename($zipPath));

        $zip->extractTo($tmpFolder);

        $result = $this->container->get(PluginBinaryUploader::class)->upload($input->getArgument('zipPath'), $this->getPluginPath($tmpFolder), $input->getOption('skipCodeReviewResult'));

        $io = new SymfonyStyle($input, $output);

        if ($result === true) {
            $io->success('Plugin zip successfully uploaded');
        } elseif ($result === false) {
            $io->warning('Code-Review check took to long');
        } else {
            $io->error(strip_tags($result));
        }
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
        $dir = current(array_filter(scandir($tmpFolder, SCANDIR_SORT_NONE), function ($value) {
            return $value[0] !== '.';
        }));

        $pluginXmlPath = $tmpFolder . '/' . $dir . '/plugin.xml';

        if (!file_exists($pluginXmlPath)) {
            throw new \RuntimeException('Cannot find a plugin.xml in zip file');
        }

        return $tmpFolder . '/' . $dir;
    }
}
