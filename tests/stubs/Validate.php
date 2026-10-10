<?php

class Validate
{
    public static function isBool($bool)
    {
        return $bool === null || is_bool($bool) || preg_match('/^(0|1)$/', $bool);
    }

    public static function isInt($value)
    {
        return ((string) (int) $value === (string) $value || $value === false);
    }
}
