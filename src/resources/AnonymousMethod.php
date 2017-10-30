<?php

namespace app;

use schibsted\payment\sdk\response\Response;

/**
 * To be used for controller payment methods
 *
 * These relevant API endpoints in Payment Gateway
 *
 * GET     /api/v{version}/anonymous-method
 * POST    /api/v{version}/anonymous-method
 * DELETE  /api/v{version}/anonymous-method/{paymentMethodId}
 * POST    /api/v{version}/anonymous-method/{paymentMethodId}/verify
 */
class AnonymousMethod extends \schibsted\payment\lib\Resource
{

    protected $name = 'v1/anonymous-method';

    const API_CREATE   = '';
    const API_DELETE   = '/{:id}';

    public function create(array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        return $this->_sdk->post($this->api(self::API_CREATE), $data);
    }

    public function delete($id, array $query = [], array $headers = [], array $options = [])
    {
        return $this->_sdk->delete($this->api(self::API_DELETE, compact('id')), []);
    }
}
