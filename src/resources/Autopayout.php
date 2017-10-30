<?php

namespace schibsted\payment\resources;

use schibsted\payment\sdk\response\Failure;

/**
 * To control autopayout feature
 *
 * Payment Gateway has these relevant api endpoints:
 *
 * POST    /api/v{version}/autoPayout body: {"fromPaymentMethodId": 12, "toPaymentMethodId": 77}
 * GET     /api/v{version}/autoPayout/{methodId}
 * DELETE  /api/v{version}/autoPayout/{methodId}
 */
class Autopayout extends \schibsted\payment\lib\Resource
{
    protected $name = 'v1/autoPayout';

    public function update($id, array $data = [], array $query = [], array $headers = [], array $options = [])
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }
}
