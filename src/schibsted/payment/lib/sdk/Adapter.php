<?php

namespace schibsted\payment\lib\sdk;

use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Failure;
use schibsted\payment\sdk\response\Error;

class Adapter extends \schibsted\payment\lib\Object implements AdapterInterface
{
    protected $_autoConfig = array('host', 'port', 'debug', 'adapter_config', 'proxy', 'log_class');
    protected $_debug = false;
    protected $_host = 'http://localhost';
    protected $_port = '80';
    protected $_adapter_config = [];
    protected $_proxy = [];
    protected $_log_class = null; // Class that supports log methods `debug`, `alert`, `warning`, `info`, `notice`

    public function execute($url, $method = 'GET', array $headers = array(), $data = null, array $options = array())
    {
        $this->_log('debug', "Query : $method : $url", "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        $result = $this->_makeRequest($url, $method, $data);
        $request_id = !empty($result['headers']['X-Request-Id']) ? $result['headers']['X-Request-Id'] : '';

        $content = json_decode($result['content'], true);

        if ($result['code'] < 400 && $content !== false && $result['code'] != 0) {
            $response = new Success(['code' => $result['code'], 'content' => $content, 'meta' => $this->_extractMeta($result)]);
            $this->_log('debug', 'Success : ' . $result['code'] . ' : ' . $request_id . ' : ' . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        } elseif ($result['code'] == 404 && isset($result['headers']['Content-Type']) && $result['headers']['Content-Type'] != 'application/json') {
            $url = !empty($result['last_url']) ? $result['last_url'] : $url;
            $message = $developer_message = "$url not found";
            $error_number = 404;
            $content = compact('error_number', 'message', 'developer_message');
            $response = new Failure(['code' => $result['code'], 'content' => $content, 'meta' => $result]);
            $this->_log('alert', "FAILURE : $error_number : $developer_message : " . $request_id . ' : ' . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        } elseif ($content && !is_array($content)) {
            $url = !empty($result['last_url']) ? $result['last_url'] : $url;
            $message = $developer_message = "$url returned invalid json (not array/object)";
            $error_number = 500;
            $content = compact('error_number', 'message', 'developer_message');
            $response = new Failure(['code' => $result['code'], 'content' => $content, 'meta' => $result]);
            $this->_log('alert', "FAILURE : $error_number : $developer_message : " . $request_id . ' : ' . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
        } elseif ($result['code'] >= 500) {
            $message = $developer_message = "Service Unavailable";
            $error_number = $result['code'];
            $content = compact('error_number', 'message', 'developer_message');
            $response = new Failure(['code' => $result['code'], 'content' => $content, 'meta' => $result]);
            $this->_log('alert', "FAILURE : $error_number : $developer_message : " . $request_id . ' : ' . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
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
                $message = !empty($content['message']) ? $content['message'] : 'Communication error';
                $this->_log('alert', 'FAILURE : ' . $result['code'] . " : $message : $request_id : " . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                if (isset($result['error'])) {
                    $this->_log('debug', $result['error'], "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                }
                if (isset($result['content']) && is_string($result['content'])) {
                    $this->_log('debug', $result['error'], "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                }
            } else {
                $response = new Error(['code' => $result['code'], 'content' => $content, 'meta' => $meta]);
                $message = !empty($content['message']) ? $content['message'] : 'Service error';
                $this->_log('error', 'ERROR : ' . $result['code'] . " : $message : $request_id : " . $result['latency'] . 's', "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                $devmsg = ($content && isset($content['errorNumber'])) ?  $content['errorNumber'] : '' .
                    ($content && isset($content['developerMessage'])) ?  $content['developerMessage'] : '' ;
                if ($devmsg) {
                    $this->_log('debug', "PMS dev msg: $devmsg", "PMS", __FILE__, __CLASS__, __FUNCTION__, __LINE__);
                }
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

    protected function _makeRequest($url, $method, $post = null)
    {
        throw new \Exception(__FUNCTION__ . " must be implemented by subclasses of " . __CLASS__);
    }

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
