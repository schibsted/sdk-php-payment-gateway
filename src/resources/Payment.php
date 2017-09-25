<?php

namespace schibsted\payment\resources;

use schibsted\payment\sdk\response\Failure;

/**
 * To be used for direct payment
 *
 * These relevant API endpoints in PMS
 *
 * (payment)   POST    /api/v{version}/payment
 * (payment)   GET     /api/v{version}/payment/{paymentId}
 * (payment)   POST    /api/v{version}/payment/{paymentId}/deposit
 * (payment)   POST    /api/v{version}/payment/{paymentId}/initialize
 * (payment)   POST    /api/v{version}/payment/{paymentId}/sale
 * (payment)   POST    /api/v{version}/payment/{paymentId}/transfer
 * (payment)   POST    /api/v{version}/payment/{paymentId}/withdraw
 *
 */
class Payment extends \schibsted\payment\lib\Resource
{

    protected $name = 'v1/payment';

    const API_DEPOSIT     = '/{:id}/deposit';
    const API_INITIALIZE  = '/{:id}/initialize';
    const API_SALE        = '/{:id}/sale';
    const API_TRANSFER    = '/{:id}/transfer';
    const API_WITHDRAW    = '/{:id}/withdraw';

    public function deposit($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_DEPOSIT, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function initialize($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_INITIALIZE, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function sale($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_SALE, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function transfer($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_TRANSFER, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function withdraw($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        $path = $this->api(self::API_WITHDRAW, compact('id'));
        return $this->_sdk->post($path, $data, $query, $headers, $options);
    }

    public function update($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }

    public function delete($id, array $query = [], array $headers = [], array $options = [])
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }
}
