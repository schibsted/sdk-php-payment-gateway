<?php

namespace schibsted\payment\tests\unit\sdk;

use schibsted\payment\sdk\Rest;
use schibsted\payment\sdk\response\Success;

class RestTest extends \PHPUnit_Framework_TestCase
{

    protected $connection_rest_test = ['connection' => ['adapter' => 'schibsted\payment\sdk\adapters\Test']];

    public function testGet()
    {
        $sdk = new Rest($this->connection_rest_test);
        $expected = ['url' => '/api/order/1', 'method' => 'GET', 'headers' => [], 'data' => null, 'options' => []];
        $result = $sdk->get('/api/order/1');
        $this->assertTrue($result instanceof Success);
        $this->assertEquals($expected, $result->getContent());
    }

    public function testPost()
    {
        $sdk = new Rest($this->connection_rest_test);
        $expected = ['url' => '/api/order/1', 'method' => 'POST', 'headers' => [], 'data' => ['name' => 'King Kong'], 'options' => []];
        $result = $sdk->post('/api/order/1', ['name' => 'King Kong']);
        $this->assertTrue($result instanceof Success);
        $this->assertEquals($expected, $result->getContent());
    }

    public function testDelete()
    {
        $sdk = new Rest($this->connection_rest_test);
        $expected = ['url' => '/api/order/1', 'method' => 'DELETE', 'headers' => [], 'data' => null, 'options' => []];
        $result = $sdk->delete('/api/order/1');
        $this->assertTrue($result instanceof Success);
        $this->assertEquals($expected, $result->getContent());
    }
}
