<?php

namespace App\Helpers;

class ArrayHelper
{

    public static function ValidateKeyData($key, $array)
    {
        $data = null;

        if (is_object($array)) {
            $data = isset($array->$key) ? $array->$key : null;
        } else if (is_array($array)) {
            $data = (isset($array[$key]) && !empty($array[$key])) ? $array[$key] : null;
        }

        return $data;
    }
}
