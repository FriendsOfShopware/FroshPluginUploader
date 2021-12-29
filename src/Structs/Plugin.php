<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Plugin extends Struct
{
    /** @var int */
    public $id;

    public static $mappedFields = [
        'producer' => \FroshPluginUploader\Structs\Producer::class,
        'type' => \FroshPluginUploader\Structs\Type::class,
        'lifecycleStatus' => \FroshPluginUploader\Structs\LifecycleStatus::class,
        'generation' => \FroshPluginUploader\Structs\Generation::class,
        'activationStatus' => \FroshPluginUploader\Structs\ActivationStatus::class,
        'approvalStatus' => \FroshPluginUploader\Structs\ApprovalStatus::class,
        'standardLocale' => \FroshPluginUploader\Structs\StandardLocale::class,
        'license' => \FroshPluginUploader\Structs\License::class,
        'infos' => \FroshPluginUploader\Structs\Infos::class,
        'priceModels' => \FroshPluginUploader\Structs\PriceModels::class,
        'variants' => \FroshPluginUploader\Structs\Variants::class,
        'storeAvailabilities' => \FroshPluginUploader\Structs\StoreAvailabilities::class,
        'categories' => \FroshPluginUploader\Structs\Categories::class,
        'addons' => \FroshPluginUploader\Structs\Addons::class,
        'demos' => \FroshPluginUploader\Structs\Demos::class,
        'latestBinary' => \FroshPluginUploader\Structs\Binary::class,
        'localizations' => \FroshPluginUploader\Structs\Localizations::class,
        'certification' => \FroshPluginUploader\Structs\Certification::class,
        'productType' => \FroshPluginUploader\Structs\ProductType::class,
        'status' => \FroshPluginUploader\Structs\Status::class,
    ];

    /** @var Producer */
    public $producer;

    /** @var Type */
    public $type;

    /** @var string */
    public $name;

    /** @var string */
    public $code;

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

    /** @var Localizations[] */
    public $localizations;

    /** @var Binary */
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
