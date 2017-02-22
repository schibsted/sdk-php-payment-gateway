<?php

namespace schibsted\payment\errors;

use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Failure;
use schibsted\payment\sdk\response\Error;
use schibsted\payment\sdk\response\Response;

class PaymentError
{
    const CATEGORY_API_FAIL     = 'API';
    const CATEGORY_USER_ERROR   = 'USER';
    const CATEGORY_SERVICE_FAIL = 'SERVICE';
    const CATEGORY_UNKNOWN      = 'UNKNOWN';

    public $category;
    public $http_code;
    public $code;
    public $message;
    public $context;

    protected static $category_map = [
        // Order
        1001 => PaymentError::CATEGORY_API_FAIL,
        1002 => PaymentError::CATEGORY_API_FAIL,
        1003 => PaymentError::CATEGORY_API_FAIL,
        1004 => PaymentError::CATEGORY_API_FAIL,
        1005 => PaymentError::CATEGORY_API_FAIL,
        1006 => PaymentError::CATEGORY_SERVICE_FAIL,
        1007 => PaymentError::CATEGORY_SERVICE_FAIL,

        // PayEx PPA
        1201 => PaymentError::CATEGORY_API_FAIL,
        1202 => PaymentError::CATEGORY_API_FAIL,
        1203 => PaymentError::CATEGORY_USER_ERROR,
        1204 => PaymentError::CATEGORY_USER_ERROR,
        1205 => PaymentError::CATEGORY_USER_ERROR,
        1206 => PaymentError::CATEGORY_USER_ERROR,
        1207 => PaymentError::CATEGORY_USER_ERROR,
        1208 => PaymentError::CATEGORY_USER_ERROR,

        // SPiD Cash
        1300 => PaymentError::CATEGORY_USER_ERROR,
        1301 => PaymentError::CATEGORY_API_FAIL,
        1302 => PaymentError::CATEGORY_API_FAIL,
        1303 => PaymentError::CATEGORY_API_FAIL,

        // Unkown service error
        9999 => PaymentError::CATEGORY_SERVICE_FAIL,
    ];

    protected static $user_error_messages = [
        1203 => 'The payment process was stopped by the card issuer. No money was deducted from your account. Please try again.',
        1204 => 'The payment process was interrupted. No money was deducted from your account. Please try again.',
        1205 => 'Your card is not valid for this type of transaction with us. No money was deducted from your account. Please try again with another card.',
        1206 => 'The payment process was stopped by the card issuer. No money was deducted from your account. Please try again.',
        1207 => 'Sorry, an error occurred in the payment process. No money was deducted from your account. Please try again.',
        1208 => 'Sorry, an error occurred in communicating with the payment provider. No money was deducted from your account. Please try again.',
        1300 => 'There are not sufficient funds on the wallet to complete this transaction. No money was deducted from your account.',
    ];

    public function __construct(Response $response)
    {
        if ($response instanceof Success) {
            throw new \Exception("Response is a SUCCESS!");
        }

        $content            = $response->getContent();

        $this->code         = (array_key_exists('errorCode', $content))    ? $content['errorCode']    : 9999;
        $this->message      = (array_key_exists('errorMessage', $content)) ? $content['errorMessage'] : 'BAD OR MISSING errorMessage';
        $this->context      = (array_key_exists('errorContext', $content)) ? $content['errorContext'] : [];
        $this->http_code    = $response->getCode();
        $this->category     = (array_key_exists($this->code, PaymentError::$category_map)) ? PaymentError::$category_map[$this->code] : PaymentError::CATEGORY_UNKNOWN; // if not set, use http code?
    }

    public function getUserMessage()
    {
        if ($this->category == PaymentError::CATEGORY_USER_ERROR && array_key_exists($this->code, PaymentError::$user_error_messages)) {
            return PaymentError::$user_error_messages[$this->code];
        }
        return 'Sorry, an error occurred in the payment process. No money was deducted from your account. Please try again.';
    }
}

/*
    protected static $defined_errors = [
        // Order
        1001, 501, 'Not implemented yet Error code returned when calling not implemented feature of Order/Payment',
        1002, 409, 'Illegal state',
        1003, 404, 'Not found',
        1004, 400, 'Unsupported PSP Error code returned when creating order/payment for not supported Payment Service Provider. API client error',
        1005, 400, 'JSON processing exception',
        1006, 500, 'Unsupported purchase flow',
        1007, 500, 'Illegal payment status',

        // PayEx PPA
        1201, 400, 'Credit card not verified',
        1202, 404, 'Credit card not found',
        1203, 400, 'Customer\'s bank declined the transaction',
        1204, 400, 'Credit card operation canceled by customer"',
        1205, 400, 'Customer\'s card is not eligible for this kind of purchase',
        1206, 400, 'Customer\'s bank declined the transaction',
        1207, 400, 'Some problem occurred with the credit card',
        1208, 400, 'Bank rejected due to suspected duplicate request',

        // SPiD Cash
        1300, 400, 'Not enough funds',
        1301, 400, 'Invalid currency',
        1302, 404, 'Wallet not found',
        1303, 400, 'Wallet not verified',

        // Unkown service error
        9999, 500, 'Internal error',
    ];
*/
