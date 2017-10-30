<?php

namespace schibsted\payment\resources;

use schibsted\payment\sdk\response\Failure;
use schibsted\payment\lib\Utilities;

/**
 * To control Wallets
 *
 * Payment Gateway has these relevant api endpoints:
 *
 * POST    /api/v{version}/wallet
 * GET     /api/v{version}/wallet/{walletId}
 */
class Wallet extends \schibsted\payment\lib\Resource
{

    protected $name = 'v1/wallet';

    const API_WALLET_OPERATIONS = '/v2/wallet/{:id}/operations';
    const API_WALLETS_BY_USER = 'v1/user/{:user_id}/wallets';

    protected $_connection_name = 'spidcash';

    public function operations($id, array $query = [], array $headers = [], array $options = [])
    {
        $api = $this->base() . Utilities::insert(self::API_WALLET_OPERATIONS, compact('id')) ;
        return $this->_sdk->get($api, $query, $headers, $options);
    }

    public function findByUserId($user_id, array $query = [], array $headers = [], array $options = [])
    {
        $api = $this->base() . '/' . Utilities::insert(self::API_WALLETS_BY_USER, compact('user_id'));
        return $this->_sdk->get($api, $query, $headers, $options);
    }

    public function update($id, array $data = array(), array $query = [], array $headers = [], array $options = [])
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }

    public function delete($id, array $query = [], array $headers = [], array $options = [])
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }
}
