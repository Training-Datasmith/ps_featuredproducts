<?php

class Tools
{
    private static $values = [];

    public static function reset()
    {
        self::$values = [];
    }

    public static function setValues(array $values)
    {
        self::$values = $values;
    }

    public static function isSubmit($key)
    {
        return array_key_exists($key, self::$values);
    }

    public static function getValue($key, $default = null)
    {
        if (array_key_exists($key, self::$values)) {
            return self::$values[$key];
        }

        return $default;
    }

    public static function getAdminTokenLite($controller)
    {
        return 'token-' . $controller;
    }
}
