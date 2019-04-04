<?php
/**
 * Created by PhpStorm.
 * User: dwayne.sharp
 * Date: 04.04.2019
 * Time: 10:50
 */

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\SBP\Client;
use FroshPluginUploader\Components\Util;
use FroshPluginUploader\Structs\Plugin;
use FroshPluginUploader\Structs\Config\PluginConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ConfigurePluginsCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this->setName('plugin:create:configs')->setDescription(
            'Creates config-files for each plugin to handle multiple plugins'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io            = new SymfonyStyle($input, $output);
        $filesystem    = new Filesystem();
        $pluginConfigs = Util::getPluginConfigs();
        $client        = $this->container->get(Client::class);
        $plugins       = $client->Producer()->getPlugins($client->Producer()->getProducer()->id);

        foreach ($plugins as $plugin) {
            if (false !== array_search($plugin->name, array_column((array)$pluginConfigs, 'name'))) {
                $this->updateConfig($plugin, $pluginConfigs[$plugin->id], $io, $filesystem);
            } else {
                $this->createConfig($plugin, $io, $filesystem);
            }
        }
    }

    /**
     * @param Plugin       $plugin
     * @param SymfonyStyle $io
     * @param Filesystem   $fs
     */
    private function createConfig($plugin, $io, $fs)
    {
        $pluginConfig             = new PluginConfig();
        $pluginConfig->id         = $plugin->id;
        $pluginConfig->name       = $plugin->name;
        $pluginConfig->version    = $plugin->latestBinary->version;
        $pluginConfig->status     = $plugin->activationStatus->description;
        $pluginConfig->lastChange = $plugin->lastChange;

        $this->setPluginPath($pluginConfig, $io, $fs);

        $this->saveYaml($pluginConfig, $fs);
        $io->success("File {$plugin->name}.yaml has been created!");
    }

    /**
     * @param Plugin       $plugin
     * @param PluginConfig $pluginConfig
     * @param SymfonyStyle $io
     * @param Filesystem   $fs
     */
    private function updateConfig($plugin, $pluginConfig, $io, $fs)
    {
        if (!isset($pluginConfig->version) || $pluginConfig->version !== $plugin->latestBinary->version) {
            $pluginConfig->version = $plugin->latestBinary->version;
            $io->success('Version was updated.');
        }

        if (!isset($pluginConfig->lastChange) || $pluginConfig->lastChange !== $plugin->lastChange) {
            $pluginConfig->lastChange = $plugin->lastChange;
            $io->success('Last change was updated.');
        }

        if (!isset($pluginConfig->status) || $pluginConfig->status !== $plugin->activationStatus->description) {
            $pluginConfig->status = $plugin->activationStatus->description;
            $io->success('Status was updated.');
        }

        $this->setPluginPath($pluginConfig, $io, $fs);

        $this->saveYaml($pluginConfig, $fs);
        $io->success("File {$pluginConfig->name}.yaml has been updated!");
    }

    /**
     * @param PluginConfig $pluginConfig
     * @param Filesystem   $fs
     */
    private function saveYaml($pluginConfig, $fs){
        $fs->dumpFile(Util::$configDirectory . $pluginConfig->name . '.yaml', Yaml::dump((array)$pluginConfig));
    }

    /**
     * @param PluginConfig $pluginConfig
     * @param SymfonyStyle $io
     * @param Filesystem   $fs
     */
    private function setPluginPath($pluginConfig, $io, $fs)
    {
        if (!isset($pluginConfig->path) || empty($pluginConfig->path)) {
            $pluginPath = $io->ask(
                "Please enter absolute path to folder that contains {$pluginConfig->name}-Folder: ",
                dirname(__DIR__)
            );

            if ($fs->exists(realpath($pluginPath) . '/' . $pluginConfig->name)) {
                $pluginConfig->path = $pluginPath;
                $io->success("Path was set. Plugin was found!");
            } else {
                $io->error(
                    "Path does not exists or no Plugin with name {$pluginConfig->name} were found. Path set to: null. Re-Run command."
                );
            }
        }
    }
}