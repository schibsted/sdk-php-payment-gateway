<?php

namespace schibsted\payment\tests\unit\lib\sdk;

use schibsted\payment\lib\sdk\Adapter as AdapterBase;
use schibsted\payment\sdk\adapters\Test;
use schibsted\payment\sdk\response\Response;
use schibsted\payment\sdk\response\Failure;
use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Error;

use schibsted\payment\tests\mocks\LogMock as Logger;

class Adapter extends AdapterBase
{
    public $response = ['content' => '', 'code' => 500];

    protected function _makeRequest($url, $method, $data)
    {
        return $this->response;
    }
}


class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @expectedException Exception
    */
    public function testExceptionOnMissingMethod()
    {
        $a = new AdapterBase();
        $a->execute('/api/2/method');
    }

    public function testLogging()
    {
        $a  = new Adapter(['log_class' => 'schibsted\payment\tests\mocks\LogMock']);
        $this->assertEmpty(Logger::$calls);
        $a->execute('/api/order');
        $expected = ['debug', 'error'];
        $this->assertEquals($expected, array_keys(Logger::$calls));
        Logger::$calls = [];
    }

    public function testSuccess()
    {
        $a = new Adapter();
        $a->response = ['content' => json_encode(['id' => 13, 'title' => 'Win']), 'code' => 200];

        $result = $a->execute('/api/get/title');
        $this->assertTrue($result instanceof Success);

        $expected = ['id' => 13, 'title' => 'Win'];
        $this->assertEquals($expected, $result->getContent());
    }
}
