<?php

namespace schibsted\payment\lib\sdk;

interface AdapterInterface
{

    public function execute($url, $method = 'GET', array $headers = array(), $data = null, array $options = array());
}
