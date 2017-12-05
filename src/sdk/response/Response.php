<?php

namespace schibsted\payment\sdk\response;

class Response extends \schibsted\payment\lib\BaseObject
{

    protected $_autoConfig = ['code', 'content', 'meta'];

    protected $_code;
    protected $_content;
    protected $_meta;

    public function getCode()
    {
        return $this->_code;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function getMeta()
    {
        return $this->_meta;
    }
}
