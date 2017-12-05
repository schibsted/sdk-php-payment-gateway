<?php

namespace schibsted\payment\tests\unit\lib;

use schibsted\payment\lib\BaseObject;

class Klassen extends BaseObject
{
    protected $_one = 'one';
    protected $_two = 'two';
    protected $_three = ['before' => true, 'changed' => false];

    protected $_autoConfig = ['one', 'two', 'three' => 'merge'];

    public function __get($name) {
        return $this->{"_$name"};
    }
}

class ObjectTest extends \PHPUnit\Framework\TestCase
{
    public function testAutoconfig()
    {
        $one = '11';
        $three = ['changed' => true, 'after' => true];
        $instance = new Klassen(compact('one', 'three'));

        $this->assertEquals('11', $instance->one);
        $this->assertEquals('two', $instance->two);
        $this->assertEquals(['before' => true, 'changed' => true, 'after' => true], $instance->three);
    }
}
