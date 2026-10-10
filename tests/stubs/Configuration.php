<?php

class Configuration
{
    private static $values = array();

    public static function reset()
    {
        self::$values = array();
    }

    public static function updateValue($key, $value)
    {
        self::$values[$key] = $value;
    }

    public static function get($key)
    {
        return array_key_exists($key, self::$values) ? self::$values[$key] : null;
    }
}
