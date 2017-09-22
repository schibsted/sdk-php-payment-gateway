<?php

namespace schibsted\payment\lib;

use schibsted\payment\sdk\Rest;

abstract class Resource
{

    const API_CREATE      = '';
    const API_GET         = '/{:id}';
    const API_UPDATE      = '/{:id}';
    const API_DELETE      = '/{:id}';

    protected $_connection_name = 'pms';
    protected $_sdk = null;

    protected $_sdk_class = '';
    protected $name;

    public function __construct(array $options = array())
    {
        $class = false;
        if (array_key_exists('sdk', $options)) {
            $sdk = $options['sdk'];
            if (is_object($sdk)) {
                $this->_sdk = $sdk;
                return;
            } elseif (is_array($sdk)) {
                $config = $sdk;
            }
        } else {
            $config = $options;
        }
        $this->_sdk = new Rest($config);
    }

    protected function base()
    {
        return '/api';
    }

    protected function path()
    {
        return $this->base() . '/' . $this->name();
    }

    public function name()
    {
        return $this->name;
    }

    public function api($api, array $params = array())
    {
        return $this->path() . Utilities::insert($api, $params);
    }

    public function get($id, array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_GET, compact('id'));
        return $this->_sdk->get($path, $query, $headers, $options);
    }

    public function create(array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_CREATE);
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function update($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_UPDATE, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function delete($id, array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_DELETE, compact('id'));
        return $this->_sdk->delete($path, $query, $headers, $options);
    }

    public function version()
    {
        $uri = $this->base() . '/version';
        return $this->_sdk->get($uri);
    }
}
