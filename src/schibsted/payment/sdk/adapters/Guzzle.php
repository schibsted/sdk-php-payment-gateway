<?php

namespace schibsted\payment\sdk\adapters;

use GuzzleHttp\Client;
use GuzzleHttp\Ring\Client\StreamHandler;

class Guzzle extends \schibsted\payment\lib\sdk\Adapter
{
    const USER_AGENT = 'payment-sdk-guzzle-5.2';

    protected $_adapter_config_defaults = [
        'timeout'   => 5,
        'connect_timeout' => 3,
        'verify' => true,
        'headers' => ['User-Agent' => self::USER_AGENT]
    ];

    protected function _makeRequest($url, $method, $post = null)
    {
        $start = microtime(true);
        $url = $this->_host . ':' . $this->_port . $url;
        $options = $this->_adapter_config + $this->_adapter_config_defaults + $this->addProxy();
        $client = new Client(['defaults' => $options, 'handler' => new StreamHandler()]);
        switch (strtoupper($method)) {
            case 'GET':
                $request = $client->createRequest('GET', $url);
                break;
            case 'POST':
                if ($post) {
                    $request = $client->createRequest('POST', $url, ['json' => $post]);
                    break;
                }
                $request = $client->createRequest('POST', $url);
                break;
            case 'DELETE':
                $request = $client->createRequest('DELETE', $url);
                break;
        }

        $result = [];
        try {
            $response = $client->send($request);
        } catch (\Exception $e) {
            $response = false;
            $result['code']  = 408;
            $result['errno'] = $e->getCode();
            $result['error'] = $e->getMessage();
        }

        if ($response) {
            $result['headers']  = $response->getHeaders();
            $result['code']     = $response->getStatusCode();
            $result['last_url'] = $response->getEffectiveUrl();
            $result['content']  = $response->getBody()->getContents();
        } else {
            $result['content']  = false;
            $result['request']  = compact('url', 'method', 'post');
        }
        $result['latency']  = microtime(true) - $start;
        return $result;

    }

    protected function addProxy()
    {
        if (empty($this->_proxy)) {
            return [];
        }

        return ['proxy' => [
            'http'  => $this->_proxy['host'] . ':' . $this->_proxy['port'],
            'https' => $this->_proxy['host'] . ':' . $this->_proxy['port']
        ]];
    }
}
