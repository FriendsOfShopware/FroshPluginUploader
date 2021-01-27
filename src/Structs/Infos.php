<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Infos extends Struct
{
    /** @var int */
    public $id;

    public static $mappedFields = ['locale' => Locale::class, 'standardLocale' => Locale::class];

    /** @var Locale */
    public $locale;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var string */
    public $installationManual;

    /** @var string */
    public $shortDescription;

    /** @var string */
    public $highlights;

    /** @var string */
    public $features;

    /** @var array */
    public $faqs;

    /** @var Tags[] */
    public $tags;

    /** @var Videos[] */
    public $videos;
}
