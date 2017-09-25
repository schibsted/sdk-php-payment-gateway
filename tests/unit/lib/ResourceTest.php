<?php

namespace schibsted\payment\tests\unit\lib;

use schibsted\payment\lib\Resource as ResourceAbstract;
use schibsted\payment\sdk\adapters\Test;
use schibsted\payment\sdk\response\Success;

class Resource extends ResourceAbstract
{
    protected $_connection_name = 'test';
    protected $name = 'res';
}

class SdkMock
{
    public $id = 18;
    public function get()
    {
        return func_get_args() + ['object id' => $this->id];
    }
}

class ResourceTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $sdk = new SdkMock();
        $sdk->id = 99;
        $resource = new Resource(compact('sdk'));
        $expected = ['/api/res/12', 'object id' => 99, [], [], []];
        $result = $resource->get(12);
        $this->assertEquals($expected, $result);

        $sdk = ['connection' => ['host' => 'http://example.com', 'adapter' => Test::class]];
        $resource = new Resource(compact('sdk'));
        $result = $resource->update(14, ['name' => 'John']);
        $this->assertTrue($result instanceof Success);
        $expected = [
            'url' => '/api/res/14',
            'method' => 'POST',
            'headers' => [],
            'data' => ['name' => 'John'],
            'options' => [],
        ];
        $this->assertEquals($expected, $result->getContent());
    }

    public function testApi()
    {
        $resource = new Resource();
        $api = '/{:id}/set/{:name}';
        $expected = '/api/res/17/set/Oslo';
        $result = $resource->api($api, ['id' => 17, 'name' => 'Oslo']);
        $this->assertEquals($expected, $result);
    }

    public function testGet()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['get'], [['adapter' => 'schibsted\payment\sdk\adapters\Test']]);
        $o = new Resource(['sdk' => $mock]);

        $mock->expects($this->once())->method('get')->with('/api/res/1', [], [], [])->will($this->returnValue(['id' => 1]));

        $expected = ['id' => 1];
        $result = $o->get(1);

        $this->assertEquals($expected, $result);
    }

    public function testCreate()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter' => 'schibsted\payment\sdk\adapters\Test']]);
        $resource = new Resource(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/res', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $resource->create([]);

        $this->assertEquals($expected, $result);
    }

    public function testUpdate()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['post'], [['adapter' => 'schibsted\payment\sdk\adapters\Test']]);
        $resource = new Resource(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('post')->with('/api/res/1', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $resource->update(1, []);

        $this->assertEquals($expected, $result);
    }

    public function testDelete()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['delete'], [['adapter' => 'schibsted\payment\sdk\adapters\Test']]);
        $resource = new Resource(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('delete')->with('/api/res/1', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $resource->delete(1);

        $this->assertEquals($expected, $result);
    }

    public function testVersion()
    {
        $mock = $this->getMock('schibsted\payment\sdk\Rest', ['get'], [['adapter' => 'schibsted\payment\sdk\adapters\Test']]);
        $resource = new Resource(['connection' => [], 'sdk' => $mock]);
        $mock->expects($this->once())->method('get')->with('/api/version', [], [], [])->will($this->returnValue(['id' => 1]));
        $expected = ['id' => 1];

        $result = $resource->version();

        $this->assertEquals($expected, $result);
    }
}
