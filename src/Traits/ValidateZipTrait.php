<?php declare(strict_types=1);

namespace FroshPluginUploader\Traits;

use function file_exists;
use RuntimeException;
use function sprintf;
use Symfony\Component\Console\Input\InputInterface;

trait ValidateZipTrait
{
    private function validateInput(InputInterface $input): void
    {
        $zipPath = $input->getArgument('zipPath');

        if (!file_exists($zipPath)) {
            throw new RuntimeException(sprintf('Given path "%s" does not exists', $zipPath));
        }
    }
}
