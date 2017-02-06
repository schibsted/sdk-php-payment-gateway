<?php

namespace schibsted\payment\resources;

/**
 * To control autopayout feature
 *
 * PMS has these relevant api endpoints:
 *
 * (spidcash)  POST    /api/v{version}/autoPayout
 * (spidcash)  GET     /api/v{version}/autoPayout/{walletId}
 * (spidcash)  DELETE  /api/v{version}/autoPayout/{walletId}
 */
class Autopayout extends \schibsted\payment\lib\Resource
{
    protected $name = 'v1/autoPayout';

    public function enable($fromPaymentMethodId, $toPaymentMethodId)
    {
        return $this->create(['fromPaymentMethodId' => $fromPaymentMethodId, 'toPaymentMethodId' => $toPaymentMethodId]);
    }

    public function disable($fromPaymentMethodId)
    {
        return $this->delete($fromPaymentMethodId);
    }
}
