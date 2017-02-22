<?php

namespace schibsted\payment\tests\unit\resources;

use schibsted\payment\resources\PaymentMethod;
use schibsted\payment\sdk\response\Failure;

class PaymentMethodTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $payment = new PaymentMethod(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/method', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $payment->create([]);

        $this->assertEquals($expected, $result);
    }

    public function testVerify()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $payment = new PaymentMethod(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/method/1/verify', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $payment->verify('1');

        $this->assertEquals($expected, $result);
    }

    public function testFind()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['get'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $payment = new PaymentMethod(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('get')->with('/api/v1/method', [], [], [])->will($this->returnValue([['id' => 1]]));
        $expected = [['id' => 1]];

        $result = $payment->find([]);

        $this->assertEquals($expected, $result);
    }

}
