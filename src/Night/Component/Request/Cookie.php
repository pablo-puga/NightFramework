<?php

namespace Night\Component\Request;


use Night\Component\Request\Exception\InvalidRequestParam;

class Cookie
{
    private $globalCookie;

    public function __construct(Array $globalCookie)
    {
        $this->globalCookie = $globalCookie;
    }

    public function getParam($param)
    {
        if (!array_key_exists($param, $this->globalCookie)) {
            InvalidRequestParam::throwDefault($param, get_class());
        }
        return $this->globalCookie[$param];
    }
}

