<?php

namespace schibsted\payment\lib;

class Connections extends Object
{
    const ENV_LOCAL = 'LOCAL';
    const ENV_PRE = 'PRE';
    const ENV_NO = 'NO';
    const ENV_PROD = 'COM';

    const AUTH_DOMAIN_PRE = 'https://identity-pre.schibsted.com';
    const AUTH_DOMAIN_NO = 'https://payment.schibsted.no';
    const AUTH_DOMAIN_PROD = 'https://login.schibsted.com';

    const GATEWAY_DOMAIN_PRE = 'https://api-gateway-stage.payment.schibsted.no';
    const GATEWAY_DOMAIN_NO = 'https://api-gateway.payment.schibsted.no';
    const GATEWAY_DOMAIN_PROD = 'https://api-gateway-com.payment.schibsted.no';

    protected static $_configs = [];
    protected static $_env = self::ENV_PRE;

    /**
     * Set configurations for $env. `host` and `auth` will be set based on $env
     *
     * @param string $env
     * @param array $config
     */
    public static function config($env, array $config = [])
    {
        if (!array_key_exists('host', $config)) {
            $config['host'] = self::hostForEnv($env);
        }
        if (!array_key_exists('auth', $config)) {
            $config['auth'] = self::authForEnv($env);
        }
        static::$_configs[$env] = $config;
    }

    protected static function hostForEnv($env)
    {
        switch ($env) {
            case self::ENV_PRE: return self::GATEWAY_DOMAIN_PRE;
            case self::ENV_NO: return self::GATEWAY_DOMAIN_NO;
            case self::ENV_PROD: return self::GATEWAY_DOMAIN_PROD;
            default:
                return self::GATEWAY_DOMAIN_PRE;
        }
    }

    protected static function authForEnv($env)
    {
        switch ($env) {
            case self::ENV_PRE: return self::AUTH_DOMAIN_PRE;
            case self::ENV_NO: return self::AUTH_DOMAIN_NO;
            case self::ENV_PROD: return self::AUTH_DOMAIN_PROD;
            default:
                return self::AUTH_DOMAIN_PRE;
        }
    }

    /**
     * Get configurations for the specified environment
     *
     * @param string $env
     * @return array
     */
    public static function getConfigForEnv($env)
    {
        if (empty(static::$_configs[$env])) {
            return [];
        }
        return static::$_configs[$env];
    }

    /**
     * Select environment that is the current one
     *
     * @param string $env name of environment
     */
    public static function setEnv($env)
    {
        self::$_env = $env;
    }

    /**
     * Returns name of the environment that is set as current
     *
     * @return string
     */
    public static function current()
    {
        return self::$_env;
    }

    /**
     * Get configs for current environment
     *
     * @return array
     */
    public static function get()
    {
        $e = self::$_env;
        return empty(self::$_configs[$e]) ? [] : self::$_configs[$e];
    }
}
