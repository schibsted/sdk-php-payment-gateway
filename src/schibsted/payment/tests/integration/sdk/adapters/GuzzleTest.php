<?php

namespace schibsted\payment\tests\integration\sdk\adapters;

use schibsted\payment\sdk\Rest;
use schibsted\payment\sdk\response\Failure;

class GuzzleTest extends Shared
{

    protected $config = [
        'adapter_config' => [
            'timeout'   => 5,
            'connect_timeout' => 3,

        ],
        'adapter' => 'schibsted\payment\sdk\adapters\Guzzle'
    ];
    protected $sdk = null;

    public function testFailure()
    {
        return $this->markTestSkipped("Disabled failure tests as it is a very slow timeout test");
        $config = [
            'host' => 'http://asasdfasdfsdf',
            'port' => '666',
            'adapter' => 'schibsted\payment\sdk\adapters\Artax',

            'adapter_config' => [
                'connect_timeout' => 1,
            ],

        ];
        $sdk = new Rest(['connection' => $config]);
        $result = $sdk->get('/api/wallet/1');

        $this->assertTrue($result instanceof Failure);
        $this->assertEquals(408, $result->getCode());
    }
}
