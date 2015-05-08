<?php

namespace schibsted\payment\tests\integration\sdk\adapters;

use schibsted\payment\sdk\Rest;
use schibsted\payment\sdk\response\Failure;
use schibsted\payment\sdk\response\Success;
use Amp\Artax\Client;

class ArtaxTest extends Shared
{

    protected $config = [
        'adapter_config' => [
            Client::OP_MS_CONNECT_TIMEOUT => 3000,
            Client::OP_MS_TRANSFER_TIMEOUT => 3000,
        ],
        'adapter' => 'schibsted\payment\sdk\adapters\Artax'
    ];
    protected $sdk = null;

    public function testFailure()
    {
        $config = [
            'host' => 'http://asasdfasdfsdf',
            'port' => '666',
            'adapter' => 'schibsted\payment\sdk\adapters\Artax',

            'adapter_config' => [
                Client::OP_MS_CONNECT_TIMEOUT   => 1,
            ],

        ];
        $sdk = new Rest(['connection' => $config]);
        $result = $sdk->get('/api/wallet/1');

        $this->assertTrue($result instanceof Failure);
        $this->assertEquals(408, $result->getCode());
    }
}
