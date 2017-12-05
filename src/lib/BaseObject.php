<?php

namespace schibsted\payment\lib;

/**
 * Base class based on Lithium's core\Object
 *
 * Features universal constructor with auto extracting of config array
 */
class BaseObject
{

    /**
     * Stores configuration information for object instances at time of construction.
     * **Do not override.** Pass any additional variables to `parent::__construct()`.
     */
    protected $_config = [];

    /**
     * Holds an array of values that should be processed on initialization. Each value should have
     * a matching protected property (prefixed with `_`) defined in the class. If the property is
     * an array, the property name should be the key and the value should be `'merge'`. See the
     * `_init()` method for more details.
     */
    protected $_autoConfig = [];

    /**
     * Initializes class configuration (`$_config`), and assigns object properties using the
     * `_init()` method, unless otherwise specified by configuration. See below for details.
     *
     * @param array $config The configuration options which will be assigned to the `$_config`
     *              property. This method accepts one configuration option:
     */
    public function __construct(array $config = []) {
        $this->_config = $config;
        $this->_init();
    }

    /**
     * Initializer function called by the constructor unless the constructor `'init'` flag is set
     * to `false`. May be used for testing purposes, where objects need to be manipulated in an
     * un-initialized state, or for high-overhead operations that require more control than the
     * constructor provides. Additionally, this method iterates over the `$_autoConfig` property
     * to automatically assign configuration settings to their corresponding properties.
     *
     * For example, given the following: {{{
     * class Bar extends \lithium\core\Object {
     *  protected $_autoConfig = array('foo');
     *  protected $_foo;
     * }
     *
     * $instance = new Bar(array('foo' => 'value'));
     * }}}
     *
     * The `$_foo` property of `$instance` would automatically be set to `'value'`. If `$_foo` was
     * an array, `$_autoConfig` could be set to `array('foo' => 'merge')`, and the constructor value
     * of `'foo'` would be merged with the default value of `$_foo` and assigned to it.
     *
     * @return void
     */
    protected function _init() {
        foreach ($this->_autoConfig as $key => $flag) {
            if (!isset($this->_config[$key]) && !isset($this->_config[$flag])) {
                continue;
            }

            if ($flag === 'merge') {
                $this->{"_{$key}"} = $this->_config[$key] + $this->{"_{$key}"};
            } else {
                $this->{"_$flag"} = $this->_config[$flag];
            }
        }
    }

}
