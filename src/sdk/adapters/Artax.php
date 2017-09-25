<?php

namespace schibsted\payment\sdk\adapters;

use Amp\Artax\Request;
use Amp\Artax\Client;
use Amp\wait;


class Artax extends \schibsted\payment\lib\sdk\Adapter
{
    const USER_AGENT = 'payment-sdk-artax-0.3';

    protected $_adapter_config_defaults = [
        Client::OP_MS_CONNECT_TIMEOUT   => 5000,
        Client::OP_MS_TRANSFER_TIMEOUT  => 30000,
        Client::OP_CRYPTO => [
            'verify_peer' => true,
        ]
    ];

    protected function _makeRequest($url, $method, $post = null)
    {
        $start = microtime(true);
        $url = $this->_host . ':' . $this->_port . $url;
        $options = $this->_adapter_config + $this->_adapter_config_defaults + $this->addProxy();
        $result = [];
        $request = new Request();
        $request->setUri($url);
        $request->setHeader('User-Agent', static::USER_AGENT);
        switch (strtoupper($method)) {
            case 'GET':
                $request->setMethod('GET');
                break;
            case 'POST':
                $request->setMethod('POST');
                if ($post) {
                    $data_string = is_string($post) ? $post : json_encode($post);
                    $request->setBody($data_string);
                    $request->setHeader('Content-Type', 'application/json');
                }
                break;
            case 'DELETE':
                $request->setMethod('DELETE');
                break;
        }

        try {
            $client = new Client();
            $client->setAllOptions($options);
            $promise = $client->request($request);
            $response = \Amp\wait($promise);
        } catch (\Exception $e) {
            $response = false;
            $result['code']  = 408;
            $result['errno'] = $e->getCode();
            $result['error'] = $e->getMessage();
        }

        if ($response) {
            $result['headers']  = array_map(function($v) {
                return is_array($v) ? current($v) : $v;
            }, $response->getAllHeaders());
            $result['code']     = $response->getStatus();
            $result['last_url'] = $response->getRequest()->getUri();
            $result['content']  = $response->getBody();

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

        return [
            Client::OP_PROXY_HTTP  => $this->_proxy['host'] . ':' . $this->_proxy['port'],
            Client::OP_PROXY_HTTPS => $this->_proxy['host'] . ':' . $this->_proxy['port']
        ];
    }
}
