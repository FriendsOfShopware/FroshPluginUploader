<?php

namespace FroshPluginUploader\Components;

class Util
{
    /**
     * @param $name
     * @param bool $default
     * @return array|bool|false|string
     */
    public static function getEnv($name, $default = false)
    {
        $var = getenv($name);

        if (!$var) {
            $var = $default;
        }

        return $var;
    }
}