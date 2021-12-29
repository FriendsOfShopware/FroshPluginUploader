<?php
declare(strict_types=1);

namespace FroshPluginUploader;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Dotenv\Dotenv;

class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        $env = getcwd() . '/.env';
        if (file_exists($env)) {
            $dotenv = new Dotenv();
            $dotenv->overload($env);
        }

        parent::__construct('FroshPluginUploader', '__VERSION__');
        $container = DependencyInjection::getContainer($this->getVersion());

        foreach (array_keys($container->findTaggedServiceIds('console.command')) as $commandName) {
            /** @var Command $command */
            $command = $container->get($commandName);

            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($container);
            }

            $this->add($command);
        }
    }
}
