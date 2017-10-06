<?php

namespace schibsted\payment\tests\unit\lib;

use schibsted\payment\lib\Connections;

class ConnectionsTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $ref_class = new \ReflectionClass(Connections::class);

        $ref_env = $ref_class->getProperty('_env');
        $ref_env->setAccessible(true);
        $ref_env->setValue(Connections::ENV_PRE);

        $ref_configs = $ref_class->getProperty('_configs');
        $ref_configs->setAccessible(true);
        $ref_configs->setValue([]);
    }

    public function testBlank()
    {
        $result = Connections::get();
        $this->assertEquals([], $result);
        $this->assertEquals(Connections::ENV_PRE, Connections::current());
    }

    public function testAddDifferentConfigToDifferentEnvs()
    {
        Connections::config(Connections::ENV_PRE, ['name' => 'value-1']);
        Connections::config(Connections::ENV_PROD, ['name' => 'value-2']);
        $expected = [
            'name' => 'value-1',
            'host' => Connections::GATEWAY_DOMAIN_PRE,
            'auth' => Connections::AUTH_DOMAIN_PRE
        ];
        $result = Connections::get();
        $this->assertEquals($expected, $result);

        Connections::setEnv(Connections::ENV_PROD);

        $result = Connections::getConfigForEnv(Connections::ENV_PRE);
        $this->assertEquals($expected, $result);

        $expected = [
            'name' => 'value-2',
            'host' => Connections::GATEWAY_DOMAIN_PROD,
            'auth' => Connections::AUTH_DOMAIN_PROD
        ];
        $result = Connections::get();
        $this->assertEquals($expected, $result);
    }
}
