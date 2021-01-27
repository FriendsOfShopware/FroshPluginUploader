<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Producer extends Struct
{
    /** @var int */
    public $id;

    /** @var string */
    public $prefix;

    public static $mappedFields = [];

    /** @var Contract */
    public $contract;

    /** @var string */
    public $name;

    /** @var Details[] */
    public $details;

    /** @var string */
    public $website;

    /** @var bool */
    public $fixed;

    /** @var bool */
    public $hasCancelledContract;

    /** @var string */
    public $iconPath;

    /** @var bool */
    public $iconIsSet;

    /** @var string */
    public $shopwareId;

    /** @var int */
    public $userId;

    /** @var int */
    public $companyId;

    /** @var string */
    public $companyName;

    /** @var string */
    public $saleMail;

    /** @var string */
    public $supportMail;

    /** @var string */
    public $ratingMail;

    /** @var string */
    public $iconUrl;

    /** @var null */
    public $cancelledContract;
}
