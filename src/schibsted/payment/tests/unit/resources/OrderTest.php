<?php

namespace schibsted\payment\tests\unit\resources;

use schibsted\payment\resources\Order;
use schibsted\payment\sdk\response\Success;

class OrderTest extends \PHPUnit_Framework_TestCase
{

    public function testFind()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['get'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $orders = new Order(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('get')->with('/api/v1/order', [], [], [])->will($this->returnValue([['id' => 1], ['id' => 2]]));
        $expected = [['id' => 1], ['id' => 2]];

        $result = $orders->find([]);

        $this->assertEquals($expected, $result);
    }

    public function testInitialize()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $order = new Order(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/order/1/initialize', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $order->initialize(1, []);

        $this->assertEquals($expected, $result);
    }

    public function testComplete()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $order = new Order(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/order/1/complete', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $order->complete(1, []);

        $this->assertEquals($expected, $result);
    }
}
