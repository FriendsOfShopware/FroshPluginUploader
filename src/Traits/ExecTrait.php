<?php
declare(strict_types=1);

namespace FroshPluginUploader\Traits;

use RuntimeException;

trait ExecTrait
{
    protected function exec(string $command): array
    {
        exec($command, $output, $ret);

        // @codeCoverageIgnoreStart
        if ($ret !== 0) {
            throw new RuntimeException(sprintf('Command "%s" failed with code %d', $command, $ret));
        }
        // @codeCoverageIgnoreEnd

        return $output;
    }
}
