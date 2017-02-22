<?php

namespace schibsted\payment\tests\unit\resources;

use schibsted\payment\resources\Autopayout;
use schibsted\payment\sdk\response\Success;

class AutopayoutTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $sdk = new \schibsted\payment\sdk\Rest(['connection' => ['adapter' => new MockAdapterAutopayoutTest()]]);
        $autpayout = new Autopayout(['sdk' => $sdk]);
        $result = $autpayout->create(['fromPaymentMethodId' => 12, 'toPaymentMethodId' => 37]);

        $expected = new Success(['code' => 201, 'content' => json_encode(['fromPaymentMethodId' => 12, 'toPaymentMethodId' => 37])]);
        $this->assertEquals($expected, $result);
    }

    public function testDelete()
    {
        $sdk = new \schibsted\payment\sdk\Rest(['connection' => ['adapter' => new MockAdapterAutopayoutTest()]]);
        $autpayout = new Autopayout(['sdk' => $sdk]);
        $result = $autpayout->delete(12);

        $expected = new Success(['code' => 204, 'content' => null]);
        $this->assertEquals($expected, $result);
    }

    public function testGet()
    {
        $sdk = new \schibsted\payment\sdk\Rest(['connection' => ['adapter' => new MockAdapterAutopayoutTest()]]);
        $autpayout = new Autopayout(['sdk' => $sdk]);

        $result = $autpayout->get(12);
        $expected = new Success(['code' => 200, 'content' => json_encode(['fromPaymentMethodId' => 12, 'toPaymentMethodId' => 37])]);
        $this->assertEquals($expected, $result);

        //not exisitng
        $result = $autpayout->get(999);
        $expected = new Success(['code' => 404, 'content' => null]);
        $this->assertEquals($expected, $result);
    }
}

class MockAdapterAutopayoutTest implements \schibsted\payment\lib\sdk\AdapterInterface
{

    public function execute($url, $method = 'GET', array $headers = [], $data = null, array $options = [])
    {
        if ($method === 'POST' && $url === '/api/v1/autoPayout') {
            $code = 201;
            $content = json_encode($data);
            return new Success(compact('code', 'content'));
        } elseif ($method === 'DELETE' && preg_match('|/api/v1/autoPayout|', $url)) {
            $code = 204;
            $content = null;
            return new Success(compact('code', 'content'));
        } elseif ($method === 'GET') {
            if ($url === '/api/v1/autoPayout/12') {
                $code = 200;
                $content = json_encode(['fromPaymentMethodId' => 12, 'toPaymentMethodId' => 37]);
            } else {
                $code = 404;
                $content = null;
            }
            return new Success(compact('code', 'content'));
        }
    }
}
