<?php

namespace schibsted\payment\resources;

use schibsted\payment\sdk\response\Failure;

/**
 * To control PMS Transactions
 *
 * PMS has these relevant API endpoints:
 *
 * (spidcash)  GET     /api/v{version}/transaction
 *
 */
class Transaction extends \schibsted\payment\lib\Resource
{

    protected $name = 'v1/transaction';

    const API_FIND     = '';

    protected $_connection_name = 'spidcash';

    public function find(array $query)
    {
        return $this->_sdk->get($this->api(self::API_FIND), $query);
    }

    public function create(array $data = array())
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }

    public function get($id, array $query = [])
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }

    public function update($id, array $data = array())
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }

    public function delete($id)
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }
}
