<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\SBP\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
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
        $client     = $this->container->get(Client::class);
        $plugins    = $client->Producer()->getPlugins($client->Producer()->getProducer()->id);
        $table      = new Table($output);
        $tblStyle   = new TableStyle();
        $alignRight = $tblStyle->setPadType(STR_PAD_LEFT);
        $table->setHeaders(['Id', 'Name', 'Status', 'Latest Version', 'Version Compatibility', 'Last Change']);


        foreach ($plugins as $plugin) {
            $fromVersion = reset($plugin->latestBinary->compatibleSoftwareVersions);
            $toVersion = end($plugin->latestBinary->compatibleSoftwareVersions);
            $table->addRow([$plugin->id, $plugin->name, $plugin->activationStatus->description, $plugin->latestBinary->version, "min {$fromVersion->name} | max {$toVersion->name}", $plugin->lastChange]);
        }
        $table->setColumnStyle(3, $alignRight);
        $table->render();

    }
}
