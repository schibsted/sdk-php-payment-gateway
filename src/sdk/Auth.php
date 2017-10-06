<?php

namespace schibsted\payment\sdk;

use schibsted\payment\lib\sdk\Adapter;
use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Response;

class Auth extends \schibsted\payment\lib\Object
{
    protected $_autoConfig = ['connection'];
    protected $_connection = [];
    protected $_adapter_class = 'schibsted\payment\sdk\adapters\Curl';
    protected $_adapter = null;

    public function _init()
    {
        parent::_init();
        if (isset($this->_connection['auth'])) {
            $this->_connection['host'] = $this->_connection['auth'];
        }
        if (array_key_exists('adapter', $this->_connection)) {
            if (is_object($this->_connection['adapter'])) {
                $this->_adapter = $this->_connection['adapter'];
                $this->_connection['adapter'] = $this->_adapter_class = get_class($this->_adapter);
            } else {
                $this->_adapter_class = $this->_connection['adapter'];
            }
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

    /**
     * Request tokens from Auth service
     *
     * @return array with `access_token`, `refresh_token` and token meta
     * @throws \Exception on failed to authenticate
     */
    public function getToken()
    {
        /**
         * @var Adapter $adapter
         */
        $adapter = $this->_adapter();
        $params = [
            'client_id'     => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'redirect_uri'  => $this->getRedirectUri(),
            'grant_type'    => 'client_credentials',
            'scope'         => '',
            'state'         => '',
        ];
        $result = $adapter->execute('/oauth/token', 'POST', [], $params, ['content' => 'form']);
        if ($result instanceof Success) {
            return $result->getContent();
        }
            $error_msg = "Unknown response";
        if ($result instanceof Response) {
            $error = $result->getContent();
            if (isset($error['error'])) {
                $error_msg = $error['error'];
            }
        }
        throw new \Exception("Failed to authorize: $error_msg");
    }

    protected function getClientId()
    {
        if (empty($this->_connection['client_id'])) {
            throw new \Exception("Missing `client_id` from connections");
        }
        return $this->_connection['client_id'];
    }

    protected function getClientSecret()
    {
        if (empty($this->_connection['secret'])) {
            throw new \Exception("Missing `secret` from connections");
        }
        return $this->_connection['secret'];
    }

    protected function getRedirectUri()
    {
        if (empty($this->_connection['redirect_uri'])) {
            throw new \Exception("Missing `redirect_uri` from connections");
        }
        return $this->_connection['redirect_uri'];
    }
}
