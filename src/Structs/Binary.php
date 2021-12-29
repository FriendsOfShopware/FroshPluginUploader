<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Binary extends Struct
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $version;

    /** @var string */
    public $creationDate;

    /** @var string */
    public $lastChangeDate;

    public static $mappedFields = ['factors' => \FroshPluginUploader\Structs\Factors::class, 'changelogs' => Changelogs::class];

    /** @var Status */
    public $status;

    /** @var Assessment */
    public $assessment;

    /** @var CompatibleSoftwareVersions[] */
    public $compatibleSoftwareVersions;

    /** @var Changelogs[] */
    public $changelogs;

    /** @var bool */
    public $ionCubeEncrypted;

    /** @var bool */
    public $licenseCheckRequired;
}
