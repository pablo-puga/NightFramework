<?php

namespace Night\Component\Request;


use Night\Component\Request\Exception\InvalidRequestParam;

class Session
{
    private $globalSession;

    public function __construct(Array $globalSession)
    {
        $this->globalSession = $globalSession;
    }

    public function getParam($param)
    {
        if (!array_key_exists($param, $this->globalSession)) {
            InvalidRequestParam::throwDefault($param, get_class());
        }
        return $this->globalSession[$param];
    }
}

