<?php

namespace schibsted\payment\resources;

use schibsted\payment\sdk\response\Failure;

/**
 * To control PMS wallets
 *
 * PMS has these relevant api endpoints:
 *
 * (spidcash)  POST    /api/v{version}/wallet
 * (spidcash)  GET     /api/v{version}/wallet/{walletId}
 */
class Wallet extends \schibsted\payment\lib\Resource
{

    protected $name = 'v1/wallet';

    protected $_connection_name = 'spidcash';

    public function update($id, array $data = array())
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }

    public function delete($id)
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }
}
