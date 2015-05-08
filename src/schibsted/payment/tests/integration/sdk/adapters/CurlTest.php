<?php

namespace schibsted\payment\tests\integration\sdk\adapters;

use schibsted\payment\sdk\Rest;
use schibsted\payment\sdk\response\Failure;
use schibsted\payment\sdk\response\Success;
use schibsted\payment\lib\Connections;

class CurlTest extends Shared
{

    protected $config = [
        'adapter_config' => [
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ],
        'adapter' => 'schibsted\payment\sdk\adapters\Curl'
    ];
    protected $sdk = null;

    public function testFailure()
    {
        $config = [
            'host' => 'http://spidpay.vm',
            'port' => '0000',

            'adapter_config' => [
                CURLOPT_CONNECTTIMEOUT => 1,
            ],

        ];
        $sdk = new Rest(['connection' => $config]);
        $result = $sdk->get('/api/wallet/1');

        $this->assertTrue($result instanceof Failure);
        $this->assertEquals(408, $result->getCode());
    }

    public function testProxy()
    {

        $config = Connections::get('curl with proxy');
        if (!$config) {
            return $this->markTestSkipped('Skipped proxy test as it is missing config');
        }
        $sdk = new Rest(['connection' => Connections::get('curl with proxy')]);
        $result = $sdk->get('/get');
        $this->assertTrue($result instanceof Success);

        $content = $result->getContent();
        $this->assertFalse(empty($content));
        $this->assertTrue(isset($content['url']));
        $this->assertTrue($content['url'] === 'http://httpbin.org/get');
    }
}
