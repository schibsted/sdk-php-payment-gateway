<?php

namespace schibsted\payment\tests\unit\lib;

use schibsted\payment\lib\Connections;

class ConnectionsTest extends \PHPUnit\Framework\TestCase
{
    public function testConfig()
    {
        $uid = getmyuid();
        Connections::config($uid, ['host' => 'ROFL', 'port' => 99]);
        $expected = ['host' => 'ROFL', 'port' => 99];
        $result = Connections::get($uid);
        $this->assertEquals($expected, $result);
    }

    public function testDefaults()
    {

        $uid = getmyuid();
        Connections::config($uid);
        $expected = ['host' => 'http://localhost', 'port' => 80];
        $result = Connections::get($uid);
        $this->assertEquals($expected, $result);
    }

    public function testGettingEmpty()
    {
        $this->assertEquals([], Connections::get('blahblah'));
    }
}
