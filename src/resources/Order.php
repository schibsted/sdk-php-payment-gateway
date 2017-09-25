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
 * (spidpay.order)     POST    /api/v{version}/order/{orderId}/authorize
 *
 */
class Order extends \schibsted\payment\lib\Resource
{

    protected $name = 'v1/order';

    const API_FIND        = '';
    const API_COMPLETE    = '/{:id}/complete';
    const API_CAPTURE     = '/{:id}/capture';
    const API_AUTHORIZE   = '/{:id}/authorize';
    const API_INITIALIZE  = '/{:id}/initialize';
    const API_CREDIT      = '/{:id}/credit';
    const API_STATUS      = '/{:id}/status';

    /**
     * Search for orders
     *
     * query params:
     *         fromUserId, toUserId, clientId, since, until, filters, pageNumber, pageSize, sort
     * filters include:
     *         sales, authorizations, deposits, transfers, withdrawals, escrows, new,
     *         initialized, partly_captured,completed, cancelled, credited, pending, failed
     */
    public function find(array $query, array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_FIND);
        return $this->_sdk->get($path, $query, $headers, $options);
    }

    public function initialize($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_INITIALIZE, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function status($id, array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_STATUS, compact('id'));
        return $this->_sdk->get($path, $query, $headers, $options);
    }

    public function complete($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_COMPLETE, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function authorize($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_AUTHORIZE, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function capture($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_CAPTURE, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function credit($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_CREDIT, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }
}
