<?php

namespace schibsted\payment\tests\mocks;

class LogMock extends \schibsted\payment\lib\Object
{
    public static $calls = [];

    public static function __callStatic($method, $args)
    {
        static::$calls[$method][] = $args;
    }
}
