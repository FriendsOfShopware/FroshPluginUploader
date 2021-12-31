<?php
declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Structs\CompatibleSoftwareVersions;
use const STR_PAD_LEFT;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** @noinspection PhpUnused */
class ListPluginsCommand extends Command
{
    private Client $client;

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    protected function configure(): void
    {
        $this
            ->setName('ext:list')
            ->setAliases(['plugin:list'])
            ->setDescription('Shows all plugins from the account')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $plugins = $this->client->Producer()->getPlugins();
        $table = new Table($output);
        $tblStyle = new TableStyle();
        $alignRight = $tblStyle->setPadType(STR_PAD_LEFT);
        $table->setHeaders(['Id', 'Name', 'Generation', 'Status', 'Latest Version', 'Version Compatibility', 'Last Change']);

        foreach ($plugins as $plugin) {
            $fromVersion = null;
            $toVersion = null;

            if (empty($plugin->name)) {
                continue;
            }

            if (isset($plugin->latestBinary) && $plugin->latestBinary->compatibleSoftwareVersions) {
                $fromVersion = reset($plugin->latestBinary->compatibleSoftwareVersions);
                $toVersion = end($plugin->latestBinary->compatibleSoftwareVersions);
            }

            if (empty($fromVersion)) {
                $fromVersion = new CompatibleSoftwareVersions();
                $fromVersion->name = 'n/a';
            }

            if (empty($toVersion)) {
                $toVersion = new CompatibleSoftwareVersions();
                $toVersion->name = 'n/a';
            }

            $generation = 'Shopware 3, 4, 5';
            if (isset($plugin->generation)) {
                $generation = $plugin->generation->description;
            }

            $table->addRow(
                [
                    $plugin->id,
                    $plugin->name,
                    $generation,
                    $plugin->activationStatus->description,
                    $plugin->latestBinary->version ?? 'n/a',
                    sprintf('min %s | max %s', $fromVersion->name, $toVersion->name),
                    $plugin->lastChange,
                ]
            );
        }
        $table->setColumnStyle(3, $alignRight);
        $table->render();

        return 0;
    }
}
