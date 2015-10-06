<?php

namespace schibsted\payment\tests\unit\resources;

use schibsted\payment\resources\Order;
use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Error;
use schibsted\payment\sdk\Rest;
use schibsted\payment\errors\PaymentError;

class OrderTest extends \PHPUnit_Framework_TestCase
{

    public function testFind()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['get'], [['adapter' => 'schibsted\payment\sdk\adapters\Test']]);
        $orders = new Order(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('get')->with('/api/v1/order', [], [], [])->will($this->returnValue([['id' => 1], ['id' => 2]]));
        $expected = [['id' => 1], ['id' => 2]];

        $result = $orders->find([]);

        $this->assertEquals($expected, $result);
    }

    public function testInitialize()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter' => 'schibsted\payment\sdk\adapters\Test']]);
        $order = new Order(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/order/1/initialize', [], [], [])->willReturn(['id' => 1]);
        $expected = ['id' => 1];

        $result = $order->initialize(1, []);

        $this->assertEquals($expected, $result);
    }

    public function testComplete()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter' => 'schibsted\payment\sdk\adapters\Test']]);
        $order = new Order(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/order/1/complete', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $order->complete(1, []);

        $this->assertEquals($expected, $result);
    }

    public function testCreateOrderWithError()
    {
        $content = [
            'errorCode' => 1203,
            'errorMessage' => 'Customer\'s bank declined the transaction',
            'serviceId' => 'PayexPPA',
            'errorContext' => []
        ];
        $adapterMock = $this->getMock('schibsted\payment\sdk\adapters\Test', ['execute']);
        $adapterMock->expects($this->once())->method('execute')->willReturn(new Error(
            ['code' => 400, 'content' => $content, 'meta' => []]
        ));
        $sdk = new Rest(['connection' => ['adapter' => $adapterMock]]);
        $order = new Order(['connection' => [], 'sdk' => $sdk]);
        $result = $sdk->post('/api/order',['name' => 'King Kong']);
        $this->assertTrue($result instanceof Error);
        $this->assertEquals($content, $result->getContent());

        $error = new PaymentError($result);
        $this->assertEquals(PaymentError::CATEGORY_USER_ERROR, $error->category);

        $expected = 'The payment process was stopped by the card issuer. No money was deducted from your account. Please try again.';
        $result = $error->getUserMessage();
        $this->assertEquals($expected, $result);
    }
}
