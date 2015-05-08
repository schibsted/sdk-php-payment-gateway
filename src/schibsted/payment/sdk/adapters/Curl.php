<?php

namespace schibsted\payment\sdk\adapters;

class Curl extends \schibsted\payment\lib\sdk\Adapter
{
    const USER_AGENT = 'payment-sdk-curl-0.3';

    protected $_adapter_config_defaults = [
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_DNS_CACHE_TIMEOUT => 0,
        CURLOPT_TIMEOUT => 30
     ];

    /**
     * Makes an HTTP request.
     *
     * @param String $uri the URI to make the request to
     * @param Array $post the parameters to use for the POST body
     * @param CurlHandler $ch optional initialized curl handle
     * @return String the response text
     */
    protected function _makeRequest($url, $method, $post = null, $ch = null)
    {
        $result = [];
        $start = microtime(true);
        if (!$ch) {
            $ch = curl_init();
        }

        $url = $this->_host . ':' . $this->_port . $url;

        $opts = $this->_adapter_config + $this->_adapter_config_defaults + $this->addProxy();
        $opts[CURLOPT_URL] = $url;
        $opts[CURLOPT_USERAGENT] = static::USER_AGENT;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        if (!isset($opts[CURLOPT_HTTPHEADER])) {
            $opts[CURLOPT_HTTPHEADER] = [];
        }
        $opts[CURLOPT_HEADER] = true;
        // $opts[CURLOPT_VERBOSE] = true;

        switch ($method) {
            case 'GET':
            case 'get':
                $opts[CURLOPT_CUSTOMREQUEST] = 'GET';
                break;
            case 'DELETE':
            case 'delete':
                $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;
            case 'POST':
            case 'post':
                $opts[CURLOPT_CUSTOMREQUEST] = 'POST';
                $opts[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
                if ($post) {
                    $data_string = is_string($post) ? $post : json_encode($post);
                    $opts[CURLOPT_POSTFIELDS] = $data_string;
                    $result['post_string'] = $data_string; // For debugging, see what was posted
                    $opts[CURLOPT_HTTPHEADER][] = 'Content-Length: ' . strlen($data_string);
                }
                break;
        }


        $opts[CURLOPT_HTTPHEADER][] = 'Expect:';

        curl_setopt_array($ch, $opts);

        try {
            $result['content']  = curl_exec($ch);
        } catch (\Exception $e) {
            // caught by curl_error below to describe issue
        }

        $result['info']     = curl_getinfo($ch);
        $result['code']     = $result['info']['http_code'];


        if ($result['content'] === false) {
            $result['errno'] = curl_errno($ch);
            $result['error'] = curl_error($ch);
            $result['request'] = compact('url', 'method', 'post');
            if ($result['errno'] == 28) {
                $result['code'] = 408;
            }
        } else {
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($result['content'], 0, $header_size);
            if ($header) {
                $result['headers'] = $this->extractHeaders($header);
            }
            $result['content'] = substr($result['content'], $header_size);
        }

        curl_close($ch);
        $result['latency']  = microtime(true) - $start;
        return $result;
    }

    public function extractHeaders($string)
    {
        $parts = explode("\n", $string);

        $result = [
            'Content-Type'  => null,
            'Date'          => null,
            'X-Request-Id'  => null,
        ];

        foreach ($parts as $part) {
            foreach ($result as $key => &$value) {
                if (strpos($part, $key) === 0) {
                    $value = trim(substr($part, strlen($key) + 2));
                }
            }
        }

        $regex = '#^(\w+/\d\.\d) (\d{3})#';

        if (preg_match($regex, $string, $matches)) {
            $result['Http'] = $matches[1];
            $result['Code'] = (int) $matches[2];
        }

        return $result;
    }

    protected function addProxy()
    {
        $proxy = [];
        if (isset($this->_proxy['host'])) {
            $proxy[CURLOPT_PROXY] = $this->_proxy['host'];
        }
        if (isset($this->_proxy['port'])) {
            $proxy[CURLOPT_PROXYPORT] = $this->_proxy['port'];
        }
        if (isset($this->_proxy['user'])) {
            $proxy[CURLOPT_PROXYUSERPWD] = $this->_proxy['user'];
        }
        if (isset($this->_proxy['pass'])) {
            $proxy[CURLOPT_PROXYUSERPWD] .= ':' . $this->_proxy['pass'];
        }
        return $proxy;
    }
}
