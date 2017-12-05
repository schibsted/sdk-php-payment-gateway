<?php

namespace schibsted\payment\lib;

class Connections extends BaseObject
{
    protected static $_configs = [];

    public static function config($name, array $config = [])
    {
        $defaults = [
            'host' => 'http://localhost',
            'port' => 80
        ];
        $config += $defaults;
        static::$_configs[$name] = $config;
    }

    public static function get($name)
    {
        if (empty(static::$_configs[$name])) {
            return [];
        }
        return static::$_configs[$name];
    }
}
