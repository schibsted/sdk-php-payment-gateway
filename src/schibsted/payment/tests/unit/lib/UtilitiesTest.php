<?php

namespace schibsted\payment\tests\unit\lib;

use schibsted\payment\lib\Utilities;

class UtilitiesTest extends \PHPUnit_Framework_TestCase
{

    public function testInsert()
    {
        $this->assertEquals("places in sun", Utilities::insert('places in sun'));

        $s = "I love {:city}";
        $this->assertEquals('I love Oslo', Utilities::insert($s, ['city' => 'Oslo']));
        $this->assertEquals('I love Bergen', Utilities::insert($s, ['city' => 'Bergen']));

        $this->assertEquals('/api/3/user/123', Utilities::insert('/api/{:version}/user/{:id}', ['version' => 3, 'id' => 123]));
    }
}
