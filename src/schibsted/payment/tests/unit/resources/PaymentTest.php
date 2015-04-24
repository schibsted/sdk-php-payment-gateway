<?php

namespace schibsted\payment\tests\unit\resources;

use schibsted\payment\resources\Payment;
use schibsted\payment\sdk\response\Failure;

class PaymentTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $payment = new Payment(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/payment', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $payment->create([]);

        $this->assertEquals($expected, $result);
    }


    public function testGet()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['get'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $payment = new Payment(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('get')->with('/api/v1/payment/1', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $payment->get(1);

        $this->assertEquals($expected, $result);
    }


    public function testDeposit()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $payment = new Payment(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/payment/1/deposit', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $payment->deposit(1, []);

        $this->assertEquals($expected, $result);
    }

    public function testInitialize()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $payment = new Payment(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/payment/1/initialize', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $payment->initialize(1, []);

        $this->assertEquals($expected, $result);
    }

    public function testSale()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $payment = new Payment(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/payment/1/sale', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $payment->sale(1, []);

        $this->assertEquals($expected, $result);
    }

    public function testTransfer()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $payment = new Payment(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/payment/1/transfer', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $payment->transfer(1, []);

        $this->assertEquals($expected, $result);
    }



    public function testWithdraw()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $payment = new Payment(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/v1/payment/1/withdraw', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $payment->withdraw(1, []);

        $this->assertEquals($expected, $result);
    }

    public function testMissing()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', [], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $wallet = new Payment(['connection' => [], 'sdk' => $mock]);

        $result = $wallet->update(15);
        $this->assertTrue($result instanceof Failure);

        $result = $wallet->delete(15);
        $this->assertTrue($result instanceof Failure);
    }
}
