<?php

namespace schibsted\payment\tests\integration\sdk\adapters;

use schibsted\payment\sdk\Rest;
use schibsted\payment\sdk\response\Failure;

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
        return $this->markTestSkipped("Disabled failure tests as it is a very slow timeout test");
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
}
