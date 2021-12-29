<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components;

use FroshPluginUploader\Components\Releases\Github\Github;
use FroshPluginUploader\Components\Releases\ReleaseInterface;
use RuntimeException;

class ReleaseFactory
{
    public function get(): ReleaseInterface
    {
        if (isset($_SERVER['GITHUB_TOKEN'])) {
            return new Github();
        }

//      Find a better way to attach the zip to gitlab
//        if (isset($_SERVER['CI_JOB_TOKEN'])) {
//            return new Gitlab();
//        }

        throw new RuntimeException('Cannot find release provider with provided environment variables');
    }
}
