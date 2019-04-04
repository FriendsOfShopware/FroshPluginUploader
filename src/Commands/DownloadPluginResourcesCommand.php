<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\ResourcesDownloader;
use FroshPluginUploader\Components\Util;
use FroshPluginUploader\Structs\Config\PluginConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class DownloadPluginResourcesCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure(): void
    {
        $this->setName('plugin:download:resources')->setDescription(
            'Downloads the resources from account to given folder. Needed for plugin:upload'
        )->addArgument('path', InputArgument::REQUIRED, 'Path to /Resources/store folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $plugins = Util::getPluginConfigs(true);

        if (empty($plugins)) {
            /* old version - should be removed later and path should be optional */
            if (!Util::getEnv('PLUGIN_ID')) {
                throw new \RuntimeException('The enviroment variable $PLUGIN_ID is required');
            }

            $this->container->get(ResourcesDownloader::class)->download($input->getArgument('path'));
        } else {
            /* new version - no path needed anymore */
            /**
             * @var PluginConfig $plugin
             */
            foreach ($plugins as $plugin) {
                putenv("PLUGIN_ID=" . $plugin->id);
                $this->container->get(ResourcesDownloader::class)->download(
                    $path = $plugin->path . '/' . $plugin->name . '/Resources/store'
                );
            }
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('Downloaded store data to given folder');
    }
}
