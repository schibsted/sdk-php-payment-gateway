<?php

namespace schibsted\payment\tests\mocks;

class LogMock extends \schibsted\payment\lib\BaseObject
{
    public static $calls = [];

    public static function __callStatic($method, $args)
    {
        static::$calls[$method][] = $args;
    }
}
