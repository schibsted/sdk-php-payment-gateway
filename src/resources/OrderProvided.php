<?php

namespace schibsted\payment\resources;

/**
 * Payment Gateway has these relevant API endpoints
 *
 * POST     /api/v{version}/provided-order
 * PATCH    /api/v{version}/provided-order
 *
 */
class OrderProvided extends \schibsted\payment\lib\Resource
{

    protected $name = 'v1/provided-order';

}
