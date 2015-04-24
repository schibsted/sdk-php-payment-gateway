<?php

namespace schibsted\payment\tests\unit\resources;

use schibsted\payment\resources\Transaction;
use schibsted\payment\sdk\response\Failure;

class TransactionTest extends \PHPUnit_Framework_TestCase
{

    public function testFind()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['get', 'post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $transaction = new Transaction(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('get')->with('/api/v1/transaction', [], [], [])->will($this->returnValue(['id' => 1]));

        $expected = ['id' => 1];
        $result = $transaction->find(array());

        $this->assertEquals($expected, $result);
    }


    public function testMissing()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', [], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $wallet = new Transaction(['connection' => [], 'sdk' => $mock]);

        $result = $wallet->create();
        $this->assertTrue($result instanceof Failure, "create is not missing!");

        $result = $wallet->update(15);
        $this->assertTrue($result instanceof Failure, "update is not missing!");

        $result = $wallet->get(15);
        $this->assertTrue($result instanceof Failure, "get is not missing!");

        $result = $wallet->delete(15);
        $this->assertTrue($result instanceof Failure, "delete is not missing!");
    }
}
