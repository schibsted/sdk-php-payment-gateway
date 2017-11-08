<?php

namespace schibsted\payment\resources;

/**
 * Pricepoints (value, currency) allowed in Fortumo PPA.
 * Orders can be created only for prices specified in configurations.
 * Order for another price is going to fail.
 */
class FortumoPriceConfiguration extends \schibsted\payment\lib\Resource
{
    protected $name = 'merchants/fortumo';
    const API_FIND = '';

    public function find(array $query)
    {
        return $this->_sdk->get($this->api(self::API_FIND), $query);
    }
}