<?php declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use FroshPluginUploader\Components\SBP\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class UpdateProducerCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('producer:update')
            ->setDescription('Cache producer and plugin informations from server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Client $client */
        $client = $this->container->get('FroshPluginUploader\Components\SBP\Client');

        $client->Producer()->getPlugins(true);

        $io = new SymfonyStyle($input, $output);
        $io->success('Producer and Plugincache has been successfully updated!');
    }
}