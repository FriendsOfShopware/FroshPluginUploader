<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Plugin extends Struct
{
    /** @var int */
    public $id;

    public static $mappedFields = [
        'producer' => 'FroshPluginUploader\Structs\Producer',
        'type' => 'FroshPluginUploader\Structs\Type',
        'lifecycleStatus' => 'FroshPluginUploader\Structs\LifecycleStatus',
        'generation' => 'FroshPluginUploader\Structs\Generation',
        'activationStatus' => 'FroshPluginUploader\Structs\ActivationStatus',
        'approvalStatus' => 'FroshPluginUploader\Structs\ApprovalStatus',
        'standardLocale' => 'FroshPluginUploader\Structs\StandardLocale',
        'license' => 'FroshPluginUploader\Structs\License',
        'infos' => 'FroshPluginUploader\Structs\Infos',
        'priceModels' => 'FroshPluginUploader\Structs\PriceModels',
        'variants' => 'FroshPluginUploader\Structs\Variants',
        'storeAvailabilities' => 'FroshPluginUploader\Structs\StoreAvailabilities',
        'categories' => 'FroshPluginUploader\Structs\Categories',
        'addons' => 'FroshPluginUploader\Structs\Addons',
        'demos' => 'FroshPluginUploader\Structs\Demos',
        'localizations' => 'FroshPluginUploader\Structs\Localizations',
        'certification' => 'FroshPluginUploader\Structs\Certification',
        'productType' => 'FroshPluginUploader\Structs\ProductType',
        'status' => 'FroshPluginUploader\Structs\Status',
    ];

    /** @var Producer */
    public $producer;

    /** @var Type */
    public $type;

    /** @var string */
    public $name;

    /** @var string */
    public $code;

    /** @var string */
    public $moduleKey;

    /** @var LifecycleStatus */
    public $lifecycleStatus;

    /** @var Generation */
    public $generation;

    /** @var ActivationStatus */
    public $activationStatus;

    /** @var ApprovalStatus */
    public $approvalStatus;

    /** @var StandardLocale */
    public $standardLocale;

    /** @var License */
    public $license;

    /** @var Infos[] */
    public $infos;

    /** @var PriceModels */
    public $priceModels;

    /** @var Variants */
    public $variants;

    /** @var StoreAvailabilities[] */
    public $storeAvailabilities;

    /** @var Categories[] */
    public $categories;

    /** @var Addons */
    public $addons;

    /** @var bool */
    public $useContactForm;

    /** @var string */
    public $lastChange;

    /** @var string */
    public $creationDate;

    /** @var bool */
    public $support;

    /** @var bool */
    public $supportOnlyCommercial;

    /** @var string */
    public $iconPath;

    /** @var bool */
    public $iconIsSet;

    /** @var string */
    public $examplePageUrl;

    /** @var Demos */
    public $demos;

    /** @var Localizations */
    public $localizations;

    /** @var null */
    public $latestBinary;

    /** @var bool */
    public $responsive;

    /** @var bool */
    public $migrationSupport;

    /** @var bool */
    public $automaticBugfixVersionCompatibility;

    /** @var bool */
    public $hiddenInStore;

    /** @var Certification */
    public $certification;

    /** @var ProductType */
    public $productType;

    /** @var Status */
    public $status;

    /** @var string */
    public $statusComment;

    /** @var null */
    public $minimumMarketingSoftwareVersion;

    /** @var bool */
    public $isSubscriptionEnabled;

    /** @var null */
    public $releaseDate;

    /** @var string */
    public $plannedReleaseDate;

    /** @var string */
    public $iconUrl;

    /** @var string */
    public $pictures;

    /** @var bool */
    public $hasPictures;

    /** @var string */
    public $binaries;

    /** @var string */
    public $comments;

    /** @var string */
    public $reviews;

    /** @var null */
    public $successor;

    /** @var null */
    public $pluginPreview;
}
