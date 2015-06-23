<?php

namespace schibsted\payment\sdk;

use schibsted\payment\sdk\response\Response;

class Rest extends \schibsted\payment\lib\Object
{

    protected $_autoConfig = array('connection');

    protected $_connection = array();
    protected $_adapter_class = 'schibsted\payment\sdk\adapters\Curl';

    protected $_adapter = null;

    protected static $_responses = [];

    public function _init()
    {
        parent::_init();
        if (array_key_exists('adapter', $this->_connection)) {
            $this->_adapter_class = $this->_connection['adapter'];
        }
    }

    protected function _adapter()
    {
        if (is_null($this->_adapter)) {
            $ac = $this->_adapter_class;
            $this->_adapter = new $ac($this->_connection);
        }
        return $this->_adapter;
    }

    public function get($path, array $query = array(), array $headers = array(), array $options = array())
    {
        $url = $this->_url($path, $query);
        $response = $this->_adapter()->execute($url, 'GET', $headers, null, $options);
        if ($response instanceof Response) {
            static::$_responses[$url][] = $response->getMeta();
        }
        return $response;
    }

    public function post($path, $data, array $query = array(), array $headers = array(), array $options = array())
    {
        $url = $this->_url($path, $query);
        $response = $this->_adapter()->execute($url, 'POST', $headers, $data, $options);
        if ($response instanceof Response) {
            static::$_responses[$url][] = $response->getMeta();
        }
        return $response;
    }

    public function delete($path, array $query = array(), array $headers = array(), array $options = array())
    {
        $url = $this->_url($path, $query);
        $response = $this->_adapter()->execute($url, 'DELETE', $headers, null, $options);
        if ($response instanceof Response) {
            static::$_responses[$url][] = $response->getMeta();
        }
        return $response;
    }

    protected function _url($path, array $query = array())
    {
        $uri = $path . (strpos($path, '?') === false && $query ? '?' : '') . http_build_query($query, null, '&');
        return $uri;
    }

    public static function getMeta()
    {
        return static::$_responses;
    }
}
