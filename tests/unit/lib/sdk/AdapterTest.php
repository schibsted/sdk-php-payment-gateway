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
    public $response = ['content' => '', 'code' => 500, 'latency' => 1];
    public $headers = [];

    protected function _makeRequest($url, $method, $data = null)
    {
        return $this->response;
    }

    protected function _setRequestHeaders(array $headers)
    {
        $this->headers = $headers;
    }
}


class AdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testLogging()
    {
        $a  = new Adapter(['log_class' => 'schibsted\payment\tests\mocks\LogMock']);
        $this->assertEmpty(Logger::$calls);
        $a->execute('/api/order');
        $expected = ['debug', 'alert'];
        $this->assertEquals($expected, array_keys(Logger::$calls));
        Logger::$calls = [];
    }

    public function testSuccess()
    {
        $a = new Adapter();
        $a->response = ['content' => json_encode(['id' => 13, 'title' => 'Win']), 'code' => 200, 'latency' => 1];

        $result = $a->execute('/api/get/title');
        $this->assertTrue($result instanceof Success);

        $expected = ['id' => 13, 'title' => 'Win'];
        $this->assertEquals($expected, $result->getContent());
    }

    public function testError()
    {
        $a = new Adapter();
        $a->response = ['content' => '', 'code' => null, 'latency' => 1];

        $result = $a->execute('/api/get/title');
        $this->assertTrue($result instanceof Error, "Result is " . get_class($result));
    }

    public function testFailure()
    {
        $a = new Adapter();
        $a->response = ['content' => 1, 'code' => 500, 'latency' => 1];

        $result = $a->execute('/api/get/title');
        $this->assertTrue($result instanceof Failure, "Result is " . get_class($result));
        $this->assertEquals(500, $result->getCode());
        $expected = [
            'errorCode' => 500, 
            'errorMessage' => '/api/get/title returned invalid json (not array/object)'
        ];
        $this->assertEquals($expected, $result->getContent());
    }
}
