<?php

namespace schibsted\payment\lib\sdk;

use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Failure;
use schibsted\payment\sdk\response\Error;

abstract class Adapter extends \schibsted\payment\lib\BaseObject implements AdapterInterface
{
    protected $_autoConfig = array('host', 'port', 'debug', 'adapter_config', 'proxy', 'log_class', 'token', 'api_key');
    protected $_debug = false;
    protected $_host = 'http://localhost';
    protected $_port = '80';
    protected $_adapter_config = [];
    protected $_proxy = [];
    protected $_log_class = null; // Class that supports log methods `debug`, `alert`, `warning`, `info`, `notice`
    protected $_token = null;
    protected $_api_key = null;

    public function execute($url, $method = 'GET', array $headers = array(), $data = null, array $options = array())
    {
        $this->_log('debug', "Query : $method : $url", "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        if ($this->_token) {
            $headers[] = "Authorization: Bearer {$this->_token}";
        } elseif ($this->_api_key) {
            $headers[] = "Api-Key: {$this->_api_key}";
        }
        $this->_setRequestHeaders($headers);
        $result = $this->_makeRequest($url, $method, $data);
        $request_id = !empty($result['headers']['X-Request-Id']) ? $result['headers']['X-Request-Id'] : '';

        $content = json_decode($result['content'], true);

        if ($result['code'] < 400 && $content !== false && $result['code'] != 0) {
            $response = new Success(['code' => $result['code'], 'content' => $content, 'meta' => $this->_extractMeta($result)]);
            $this->_log('debug', 'Success : ' . $result['code'] . ' : ' . $request_id . ' : ' . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        } elseif ($result['code'] == 404 && isset($result['headers']['Content-Type']) && $result['headers']['Content-Type'] != 'application/json') {
            $url = !empty($result['last_url']) ? $result['last_url'] : $url;
            $content = ['errorCode' => 404, 'errorMessage' => "$url not found"];
            $response = new Failure(['code' => $result['code'], 'content' => $content, 'meta' => $result]);
            $this->_log('alert', "FAILURE : {$content['errorCode']} : {$content['errorMessage']} : " . $request_id . ' : ' . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        } elseif ($content && !is_array($content)) {
            $url = !empty($result['last_url']) ? $result['last_url'] : $url;
            $content = ['errorCode' => 500, 'errorMessage' => "$url returned invalid json (not array/object)"];
            $response = new Failure(['code' => $result['code'], 'content' => $content, 'meta' => $result]);
            $this->_log('alert', "FAILURE : {$content['errorCode']} : {$content['errorMessage']} : " . $request_id . ' : ' . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        } elseif ($result['code'] >= 500) {
            $content = ['errorCode' => $result['code'], 'errorMessage' => 'Service Unavailable'];
            $response = new Failure(['code' => $result['code'], 'content' => $content, 'meta' => $result]);
            $this->_log('alert', "FAILURE : {$content['errorCode']} : {$content['errorMessage']} : " . $request_id . ' : ' . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        } else {
            $meta = $result;
            unset($meta['content']);
            if (array_key_exists('errno', $result)) {
                if (empty($result['code']) && isset($result['errno'])) {
                    $result['code'] = $result['errno'];
                }
                if (empty($content) && isset($result['error'])) {
                    $content = $result['error'];
                }
                $response = new Failure(['code' => $result['code'], 'content' => $content, 'meta' => $meta]);
                $message = !empty($content['errorMessage']) ? $content['errorMessage'] : 'Communication error';
                $this->_log('alert', 'FAILURE : ' . $result['code'] . " : $message : $request_id : " . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                if (isset($result['error'])) {
                    $this->_log('debug', $result['error'], "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                }
                if (isset($result['content']) && is_string($result['content'])) {
                    $this->_log('debug', $result['error'], "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                }
            } else {
                $response = new Error(['code' => $result['code'], 'content' => $content, 'meta' => $meta]);
                $message = !empty($content['errorMessage']) ? $content['errorMessage'] : 'Service error';
                $this->_log('debug', 'ERROR : ' . $result['code'] . " : $message : $request_id : " . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
            }

        }
        return $response;
    }

    protected function _log($level, $message, $category = "PMS", $file = false, $class = false, $function = false, $line = false)
    {
        if (class_exists($this->_log_class, false)) {
            $class = $this->_log_class;
            $class::$level($message, $category, $file, $class, $function, $line);
        }
    }

    abstract protected function _setRequestHeaders(array $headers);

    abstract protected function _makeRequest($url, $method, $post = null);

    protected function _extractMeta(array $result)
    {
        if (array_key_exists('headers', $result)) {
            foreach ($result['headers'] as $key => $value) {
                $parts = explode('-', $key);
                if (count($parts) === 3 && $parts[1] == 'Pagination') {
                    $result['pagination'][$parts[2]] = $result['headers'][$key];
                    unset($result['headers'][$key]);
                }
            }
        }
        unset($result['content']); // content is never part of meta
        return $result;
    }
}
