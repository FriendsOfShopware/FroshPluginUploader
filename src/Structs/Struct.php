<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace FroshPluginUploader\Structs;

use function is_array;
use function is_object;
use stdClass;

class Struct
{
    public static $mappedFields = [];

    /**
     * @return static
     */
    public static function map(stdClass $object): self
    {
        $newObject = new static();

        foreach (get_object_vars($object) as $key => $value) {
            if (empty($value)) {
                $newObject->{$key} = $value;
                continue;
            }

            if (isset(static::$mappedFields[$key])) {
                if (is_array($value) && is_object($value[0])) {
                    $data = [];

                    foreach ($value as $item) {
                        $data[] = static::$mappedFields[$key]::map($item);
                    }

                    $newObject->{$key} = $data;
                } else {
                    $newObject->{$key} = static::$mappedFields[$key]::map($value);
                }

                continue;
            }

            $newObject->{$key} = $value;
        }

        return $newObject;
    }

    /**
     * @return static[]
     */
    public static function mapList(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        return array_map(static function ($item) {
            return static::map($item);
        }, $data);
    }

    /**
     * @return static
     */
    public static function make(array $data): self
    {
        $newObject = new static();

        foreach ($data as $key => $value) {
            if (empty($value)) {
                $newObject->{$key} = $value;
                continue;
            }

            if (isset(static::$mappedFields[$key])) {
                if (is_array($value) && isset($value[0])) {
                    $data = [];

                    foreach ($value as $item) {
                        $data[] = static::$mappedFields[$key]::make($item);
                    }

                    $newObject->{$key} = $data;
                } else {
                    $newObject->{$key} = static::$mappedFields[$key]::make($value);
                }

                continue;
            }

            $newObject->{$key} = $value;
        }

        return $newObject;
    }

    /**
     * @return static[]
     */
    public static function makeList(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        return array_map(static function ($item) {
            return static::make($item);
        }, $data);
    }
}
