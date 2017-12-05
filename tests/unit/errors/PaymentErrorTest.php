<?php

namespace schibsted\payment\tests\unit\errors;

use schibsted\payment\sdk\response\Error;
use schibsted\payment\errors\PaymentError;

class PaymentErrorTest extends \PHPUnit\Framework\TestCase
{
    public function testUserError()
    {
        $content = [
            'errorCode' => 1203,
            'errorMessage' => 'Customer\'s bank declined the transaction',
            'serviceId' => 'PayexPPA',
            'errorContext' => []
        ];
        $response = new Error(['code' => 400, 'content' => $content, 'meta' => []]);

        $error = new PaymentError($response);
        $this->assertEquals(PaymentError::CATEGORY_USER_ERROR, $error->category);
    }
}