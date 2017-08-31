<?php

namespace schibsted\payment\resources;

/**
 * To control PMS provided orders
 *
 * PMS has these relevant API endpoints
 *
 * (spidpay.order)     POST     /api/v{version}/provided-order
 * (spidpay.order)     PATCH    /api/v{version}/provided-order
 *
 */
class OrderProvided extends \schibsted\payment\lib\Resource
{

    protected $name = 'v1/provided-order';

}
