<?php

namespace schibsted\payment\resources;

/**
 * To control PMS orders
 *
 * PMS has these relevant API endpoints
 *
 * (spidpay.order)     GET     /api/v{version}/order
 *
 * (spidpay.order)     POST    /api/v{version}/order
 * (spidpay.order)     GET     /api/v{version}/order/{orderId}
 * (spidpay.order)     POST    /api/v{version}/order/{orderId}
 * (spidpay.order)     DELETE  /api/v{version}/order/{orderId}
 *
 * (spidpay.order)     POST    /api/v{version}/order/{orderId}/initialize
 * (spidpay.order)     POST    /api/v{version}/order/{orderId}/complete
 *
 */
class Order extends \schibsted\payment\lib\Resource
{

    protected $name = 'v1/order';

    const API_FIND        = '';
    const API_COMPLETE    = '/{:id}/complete';
    const API_INITIALIZE  = '/{:id}/initialize';

    public function find(array $query)
    {
        return $this->_sdk->get($this->api(self::API_FIND), $query);
    }

    public function initialize($id, array $data = array())
    {
        return $this->_sdk->post($this->api(self::API_INITIALIZE, compact('id')), $data);
    }

    public function complete($id, array $data = array())
    {
        return $this->_sdk->post($this->api(self::API_COMPLETE, compact('id')), $data);
    }
}
