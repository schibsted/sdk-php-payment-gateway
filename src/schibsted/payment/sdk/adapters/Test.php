<?php

namespace schibsted\payment\sdk\adapters;

use schibsted\payment\sdk\response\Failure;
use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Error;

class Test implements \schibsted\payment\lib\sdk\AdapterInterface
{

    public function execute($url, $method = 'GET', array $headers = array(), $data = null, array $options = array())
    {
        $content = compact('url', 'method', 'headers', 'data', 'options');
        switch ($url) {
            case '/error/406':
                $code = 406;
                return new Error(compact('code', 'content'));
            break;
            case '/failure/501':
                $code = 501;
                return new Failure(compact('code', 'content'));
            break;
            default:
                $code = 200;
                return new Success(compact('code', 'content'));
            break;
        }
    }
}
