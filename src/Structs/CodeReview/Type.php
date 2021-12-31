<?php
declare(strict_types=1);

namespace FroshPluginUploader\Structs\CodeReview;

use FroshPluginUploader\Structs\Struct;

class Type extends Struct
{
    public int $id;

    public string $name;

    public string $description;
}
