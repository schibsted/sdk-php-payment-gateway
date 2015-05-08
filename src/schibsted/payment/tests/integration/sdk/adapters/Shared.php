<?php

namespace schibsted\payment\tests\integration\sdk\adapters;

use schibsted\payment\sdk\Rest;
use schibsted\payment\sdk\response\Failure;
use schibsted\payment\sdk\response\Success;
use schibsted\payment\lib\Connections;

class Shared extends \PHPUnit_Framework_TestCase
{

    protected $connection_name = 'shared';

    public function setUp()
    {
        $config = Connections::get($this->connection_name);
        $config += $this->config;
        if (!$config) {
            return $this->markTestSkipped('Skipped Integration as connection is not defined.');
        }
        $this->sdk = new Rest(['connection' => $config]);
    }

    private function userAgent()
    {
        $class = $this->config['adapter'];
        return $class::USER_AGENT;
    }

    public function testGet()
    {
        $response = $this->sdk->get('/get');
        $this->assertTrue($response instanceof Success);

        $content = $response->getContent();
        $this->assertFalse(empty($content));
        $this->assertTrue(isset($content['url']));
        $this->assertTrue($content['url'] === 'http://httpbin.org/get');
    }

    public function testCreate()
    {
        $data = [
            'purchaseFlow' => 'SALE',
            'description' => 'This is a test order',
            'redirectUrl' => 'http://sdk.dev?orderId={orderId}',
            'orderItems' => [['quantity' => 1, 'type' => 'DEFAULT', 'price' => 10000, 'vat' => 0, 'description' => 'Test product']]

        ];
        $response = $this->sdk->post('/post', $data);
        $this->assertTrue($response instanceof Success, get_class($response) . ' ' . print_r($response->getContent(), true));

        $content = $response->getContent();
        $this->assertFalse(empty($content));
        $this->assertEquals($this->userAgent(), $content['headers']['User-Agent']);
        $this->assertEquals($data, $content['json']);

    }

    public function testOrderInit()
    {
        $response = $this->sdk->post("/post", []);
        $this->assertTrue($response instanceof Success);
        $content = $response->getContent();
        $this->assertFalse(empty($content));
        $this->assertEquals($this->userAgent(), $content['headers']['User-Agent']);
    }

    public function testDelete()
    {

        $response = $this->sdk->delete("/delete", []);
        $this->assertTrue($response instanceof Success);
        $this->assertEquals(200, $response->getCode());
    }
}
