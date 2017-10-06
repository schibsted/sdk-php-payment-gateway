<?php

namespace schibsted\payment\tests\unit\sdk;

use schibsted\payment\lib\sdk\Adapter;
use schibsted\payment\sdk\Auth;
use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Error;

class AuthTest extends \PHPUnit_Framework_TestCase
{

    protected $connection_rest_test = ['connection' => ['adapter' => 'schibsted\payment\sdk\adapters\Test']];

    public function testGetToken()
    {
        $adapter = $this->getMockBuilder(Adapter::class)
            ->setMethods(['execute', '_setRequestHeaders', '_makeRequest'])
            ->getMock();
        $params = [
            'client_id'     => 'client123',
            'client_secret' => 'clientsecret',
            'redirect_uri'  => 'https://example.com',
            'grant_type'    => 'client_credentials',
            'scope'         => '',
            'state'         => '',
        ];
        $adapter->expects($this->once())
            ->method('execute')
            ->with('/oauth/token', 'POST', [], $params, ['content' => 'form'])
            ->willReturn(new Success(['code' => 200, 'content' => ['access_token' => 'token1234']]));
        $auth = new Auth(['connection' => [
            'auth' => 'https://identity-pre.schibsted.com',
            'client_id' => 'client123',
            'secret' => 'clientsecret',
            'redirect_uri' => 'https://example.com',
            'adapter' => $adapter
        ]]);
        $token = $auth->getToken();
        $this->assertArrayHasKey('access_token', $token);
    }

    public function testGetTokenBadSecret()
    {
        $adapter = $this->getMockBuilder(Adapter::class)
            ->setMethods(['execute', '_setRequestHeaders', '_makeRequest'])
            ->getMock();
        $params = [
            'client_id'     => 'client123',
            'client_secret' => 'badsecret',
            'redirect_uri'  => 'https://example.com',
            'grant_type'    => 'client_credentials',
            'scope'         => '',
            'state'         => '',
        ];
        $adapter->expects($this->once())
            ->method('execute')
            ->with('/oauth/token', 'POST', [], $params, ['content' => 'form'])
            ->willReturn(new Error(['code' => 403, 'content' => ['error' => 'Invalid credentials']]));
        $auth = new Auth(['connection' => [
            'auth' => 'https://identity-pre.schibsted.com',
            'client_id' => 'client123',
            'secret' => 'badsecret',
            'redirect_uri' => 'https://example.com',
            'adapter' => $adapter
        ]]);

        $this->setExpectedException(\Exception::class, 'Invalid credentials');
        $token = $auth->getToken();
    }

}
