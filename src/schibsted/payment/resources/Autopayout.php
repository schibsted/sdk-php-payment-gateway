<?php

namespace schibsted\payment\resources;

use schibsted\payment\sdk\response\Failure;

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

    public function update($id, array $data = array())
    {
        return new Failure(['code' => 501, 'content' => 'Not implemented']);
    }
}
