<?php

namespace schibsted\payment\lib\sdk;

use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Failure;
use schibsted\payment\sdk\response\Error;
use observation\Log;

class Adapter extends \schibsted\payment\lib\Object implements AdapterInterface
{
    protected $_autoConfig = array('host', 'port', 'debug', 'adapter_config', 'proxy');
    protected $_debug = false;
    protected $_host = 'http://localhost';
    protected $_port = '80';
    protected $_adapter_config = [];
    protected $_proxy = [];

    public function execute($url, $method = 'GET', array $headers = array(), $data = null, array $options = array())
    {
        Log::debug('Query: ' . $method . ' ' . $url, "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        $result = $this->_makeRequest($url, $method, $data);
        $request_id = !empty($result['headers']['X-Request-Id']) ? $result['headers']['X-Request-Id'] : '';

        $content = json_decode($result['content'], true);

        if ($result['code'] < 400 && $content !== false && $result['code'] != 0) {
            $response = new Success(['code' => $result['code'], 'content' => $content]);
            Log::debug('Result: ' . $result['code'] . ' : Success : ' . $request_id, "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        } elseif ($result['code'] == 404 && isset($result['headers']['Content-Type']) && $result['headers']['Content-Type'] != 'application/json') {
                $url = !empty($result['last_url']) ? $result['last_url'] : $url;
                $message = $developer_message = "$url not found";
                $error_number = 404;
                $content = compact('error_number', 'message', 'developer_message');
                $response = new Failure(['code' => $result['code'], 'content' => $content, 'meta' => $result]);
                Log::alert("Result: 404 : Failure : $developer_message : " . $request_id, "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        } else {
            $meta = $result;
            unset($meta['code']);
            unset($meta['content']);
            if (array_key_exists('errno', $result)) {
                if (empty($result['code']) && isset($result['errno'])) {
                    $result['code'] = $result['errno'];
                }
                if (empty($content) && isset($result['error'])) {
                    $content = $result['error'];
                }
                $response = new Failure(['code' => $result['code'], 'content' => $content, 'meta' => $meta]);
                $message = !empty($content['message']) ? $content['message'] : 'Communication error';
                Log::alert('Result: ' . $result['code'] . " : Failure : $message : " . $request_id, "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                if (isset($result['error'])) {
                    Log::debug($result['error'], "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                }
                if (isset($result['content']) && is_string($result['content'])) {
                    Log::debug($result['error'], "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                }
            } else {
                $response = new Error(['code' => $result['code'], 'content' => $content, 'meta' => $meta]);
                $message = !empty($content['message']) ? $content['message'] : 'Service error';
                Log::error('Result: ' . $result['code'] . " : Error : $message", "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                $devmsg = ($content && isset($content['errorNumber'])) ?  $content['errorNumber'] : '' .
                    ($content && isset($content['developerMessage'])) ?  $content['developerMessage'] : '' ;
                if ($devmsg) {
                    Log::debug("PMS dev msg: $devmsg", "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                } else {
                    Log::debug(print_r($result, true), "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                }
            }

        }
        return $response;
    }

    protected function _makeRequest($url, $method, $post = null)
    {
        throw new \Exception(__FUNCTION__ . " must be implemented by subclasses of " . __CLASS__);
    }
}
