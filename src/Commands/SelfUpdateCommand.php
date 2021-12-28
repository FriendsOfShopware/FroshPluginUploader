<?php
declare(strict_types=1);

namespace FroshPluginUploader\Commands;

use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @codeCoverageIgnore
 */
class SelfUpdateCommand extends Command
{
    private string $currentVersion;

    public function __construct(string $currentVersion)
    {
        parent::__construct();
        $this->currentVersion = $currentVersion;
    }

    public static $defaultName = 'self-update';

    public static $defaultDescription = 'Update uploader to latest version';

    public function configure(): void
    {
        $this->addOption('rollback', null, InputOption::VALUE_OPTIONAL, 'Rollback to previous version');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $updater = new Updater(null, false, Updater::STRATEGY_SHA512);
        $updater->setStrategyObject(new GithubStrategy());
        $updater->getStrategy()->setPackageName('frosh/plugin-uploader');
        $updater->getStrategy()->setPharName('frosh-plugin-upload.phar');
        $updater->getStrategy()->setCurrentLocalVersion($this->currentVersion);

        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('rollback')) {
            if ($updater->rollback()) {
                $io->success('Rollback successfully to old version');

                return self::SUCCESS;
            }
            $io->error('Could not rollback to old version');

            return self::FAILURE;
        }

        if ($updater->hasUpdate()) {
            $updater->update();

            $io->success(sprintf('Updated from %s to %s. Run self-update --rollback to revert back to previous version', $this->currentVersion, $updater->getNewVersion()));

            return self::SUCCESS;
        }

        $io->success('You are already on the newest version');

        return self::SUCCESS;
    }
}
