<?php

namespace schibsted\payment\sdk\adapters;

use schibsted\payment\sdk\response\Response;

class Void implements \schibsted\payment\lib\sdk\AdapterInterface
{

    public function execute($url, $method = 'GET', array $headers = array(), $data = null, array $options = array())
    {
        return new Response(['code' => 200]);
    }
}
