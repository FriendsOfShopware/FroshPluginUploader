<?php declare(strict_types=1);

namespace FroshPluginUploader\Structs;

class Struct
{
    public static $mappedFields = [];

    /**
     * @param \stdClass $object
     *
     * @return static
     */
    public static function map(\stdClass $object)
    {
        $newObject = new static();

        foreach (get_object_vars($object) as $key => $value) {
            if (empty($value)) {
                $newObject->$key = $value;
                continue;
            }

            if (isset(static::$mappedFields[$key])) {
                if (is_array($value) && is_object($value[0])) {
                    $data = [];

                    foreach ($value as $item) {
                        $data[] = static::$mappedFields[$key]::map($item);
                    }

                    $newObject->$key = $data;
                } else {
                    $newObject->$key = static::$mappedFields[$key]::map($value);
                }

                continue;
            }

            $newObject->$key = $value;
        }

        return $newObject;
    }

    /**
     * @param array $data
     *
     * @return static[]
     */
    public static function mapList(array $data)
    {
        if (empty($data)) {
            return [];
        }

        return array_map(function ($item) {
            return static::map($item);
        }, $data);
    }
}
