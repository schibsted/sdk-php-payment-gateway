<?php

namespace schibsted\payment\tests\unit\sdk;

use schibsted\payment\sdk\adapters\VoidAdapter;
use schibsted\payment\sdk\adapters\Test;
use schibsted\payment\sdk\response\Response;
use schibsted\payment\sdk\response\Failure;
use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Error;

class ResponseTest extends \PHPUnit\Framework\TestCase
{

    public function test()
    {
        $success = new Success(['code' => 200, 'content' => 'OK', 'meta' => ['ts' => 102]]);
        $this->assertEquals(200, $success->getCode());
        $this->assertEquals('OK', $success->getContent());
        $this->assertEquals(['ts' => 102], $success->getMeta());
    }

    public function testGetResponseFromVoid()
    {
        $void = new VoidAdapter();
        $result = $void->execute('/api/order/1');
        $this->assertTrue($result instanceof Response);
        $this->assertEquals(200, $result->getCode());
    }

    public function testDifferentResponses()
    {
        $test = new Test();
        $result = $test->execute('/error/406', 'POST');
        $this->assertTrue($result instanceof Error);
        $this->assertEquals(406, $result->getCode());

        $result = $test->execute('/failure/501', 'POST');
        $this->assertTrue($result instanceof Failure);
        $this->assertEquals(501, $result->getCode());
    }
}
