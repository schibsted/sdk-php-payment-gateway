<?php

namespace schibsted\payment\lib;

use L10n_String;
use datasources\Connections;
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
            $connection = Connections::get('pms.payment.' . $this->_connection_name, ['config' => true]);
            $config = ['connection' => $connection];
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
        return $this->path() . L10n_String::insert($api, $params);
    }

    public function get($id, array $query = [])
    {
        return $this->_sdk->get($this->api(self::API_GET, compact('id')), $query);
    }

    public function create(array $data = array())
    {
        return $this->_sdk->post($this->api(self::API_CREATE), $data);
    }

    public function update($id, array $data = array())
    {
        return $this->_sdk->post($this->api(self::API_UPDATE, compact('id')), $data);
    }

    public function delete($id)
    {
        return $this->_sdk->delete($this->api(self::API_DELETE, compact('id')));
    }

    public function version()
    {
        $uri = $this->base() . '/version';
        return $this->_sdk->get($uri);
    }
}
