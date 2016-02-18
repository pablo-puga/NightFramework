<?php

namespace Night\Component\Request;


use Night\Component\Request\Exception\InvalidRequestParam;

class Get
{
    private $globalGet;

    public function __construct(Array $globalGet)
    {
        $this->globalGet = $globalGet;
    }

    public function getParam($param)
    {
        if (!array_key_exists($param, $this->globalGet)) {
            InvalidRequestParam::throwDefault($param, get_class());
        }
        return $this->globalGet[$param];
    }
}

