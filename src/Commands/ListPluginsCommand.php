<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\SBP\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ListPluginsCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure(): void
    {
        $this
            ->setName('plugin:list')
            ->setDescription('Shows all plugins from the account');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $client = $this->container->get(Client::class);

        $plugins = $client->Producer()->getPlugins($client->Producer()->getProducer()->id);

        $table = new Table($output);
        $table->setHeaders(['Id', 'Name', 'Status', 'Latest Version', 'Last Change']);

        foreach ($plugins as $plugin) {
            $table->addRow([$plugin->id, $plugin->name, $plugin->activationStatus->description, $plugin->latestBinary->version, $plugin->lastChange]);
        }

        $table->render();
    }
}
