<?php

namespace schibsted\payment\lib;

class Utilities
{
    public static function insert($string, array $params = [])
    {
        if (empty($params)) return $string;
        $replace = array();
        foreach ($params as $key => $value) {
            $replace["{:{$key}}"] = $value;
        }
        $str = strtr($string, $replace);
        return $str;
    }
}
