<?php

namespace Night\Component\Request;


class Server
{
    private $globalServer;

    public function __construct(Array $globalServer)
    {
        $this->globalServer = $globalServer;
    }

    public function getParam($param)
    {
        return $this->globalServer[$param];
    }
}

