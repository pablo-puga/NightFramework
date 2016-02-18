<?php

namespace Night\Component\Request;


use Night\Component\Request\Exception\InvalidRequestParam;

class Server
{
    private $globalServer;

    public function __construct(Array $globalServer)
    {
        $this->globalServer = $globalServer;
    }

    public function getParam($param)
    {
        if (!array_key_exists($param, $this->globalServer)) {
            InvalidRequestParam::throwDefault($param, get_class());
        }
        return $this->globalServer[$param];
    }
}

