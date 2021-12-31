<?php
/** @noinspection PhpMissingFieldTypeInspection */
/** @noinspection PhpMissingFieldTypeInspection */
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Plugin extends Struct
{
    /** @var int */
    public $id;

    public static $mappedFields = [
        'producer' => Producer::class,
        'type' => Type::class,
        'lifecycleStatus' => LifecycleStatus::class,
        'generation' => Generation::class,
        'activationStatus' => ActivationStatus::class,
        'approvalStatus' => ApprovalStatus::class,
        'standardLocale' => StandardLocale::class,
        'license' => License::class,
        'infos' => Infos::class,
        'storeAvailabilities' => StoreAvailabilities::class,
        'categories' => Categories::class,
        'latestBinary' => Binary::class,
        'localizations' => Localizations::class,
        'certification' => Certification::class,
        'productType' => ProductType::class,
        'status' => Status::class,
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

    /** @var StoreAvailabilities[] */
    public $storeAvailabilities;

    /** @var Categories[] */
    public $categories;

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
