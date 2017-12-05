<?php

namespace schibsted\payment\tests\unit\resources;

use schibsted\payment\resources\Wallet;
use schibsted\payment\sdk\response\Failure;

class WalletTest extends \PHPUnit\Framework\TestCase
{
    public function testGetWallet()
    {
        $mock = $this->createMock('schibsted\payment\sdk\Rest', ['get', 'post'], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $wallet = new Wallet(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('get')->with('/api/v1/wallet/1', [], [], [])->will($this->returnValue(['id' => 1]));

        $expected = ['id' => 1];
        $result = $wallet->get(1);

        $this->assertEquals($expected, $result);
    }

    public function testMissing()
    {
        $mock = $this->createMock('schibsted\payment\sdk\Rest', [], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $wallet = new Wallet(['connection' => [], 'sdk' => $mock]);

        $result = $wallet->update(15);
        $this->assertTrue($result instanceof Failure);

        $result = $wallet->delete(15);
        $this->assertTrue($result instanceof Failure);
    }

    public function testGetOperations()
    {
        $mock = $this->createMock('schibsted\payment\sdk\Rest', [], [['adapter_class' => 'schibsted\payment\sdk\adapters\Test']]);
        $wallet = new Wallet(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('get')->with('/api/v2/wallet/12/operations', [], [], []);
        $wallet->operations(12);
    }
}
