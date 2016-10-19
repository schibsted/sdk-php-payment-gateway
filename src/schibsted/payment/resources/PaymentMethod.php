<?php

namespace schibsted\payment\resources;

use schibsted\payment\sdk\response\Failure;

/**
 * To be used for controller payment methods
 *
 * These relevant API endpoints in PMS
 *
 * (spidpay.payment) GET     /api/v{version}/method
 * (spidpay.payment) POST    /api/v{version}/method
 * (spidpay.payment) DELETE  /api/v{version}/method/{paymentMethodId}
 * (spidpay.payment) POST    /api/v{version}/method/{paymentMethodId}/verify
 */
class PaymentMethod extends \schibsted\payment\lib\Resource
{

    protected $name = 'v1/method';

    const API_FIND        = '';
    const API_CREATE      = '';
    const API_VERIFY      = '/{:id}/verify';
    const API_TRANSLATE   = '/externalId/{:identifier_id}';

    public function find(array $query)
    {
        return $this->_sdk->get($this->api(self::API_FIND), $query);
    }

    public function translate($identifier_id)
    {
        return $this->_sdk->get($this->api(self::API_TRANSLATE, compact('identifier_id')));
    }

    public function create(array $data = array())
    {
        return $this->_sdk->post($this->api(self::API_CREATE), $data);
    }

    public function verify($id, array $data = [])
    {
        return $this->_sdk->post($this->api(self::API_VERIFY, compact('id')), $data);
    }

    public function update($id, array $data = array())
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }
}
