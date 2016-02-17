<?php

namespace Night\Component\Request;


class Session
{
    private $globalSession;

    public function __construct(Array $globalSession)
    {
        $this->globalSession = $globalSession;
    }

    public function getParam($param)
    {
        return $this->globalSession[$param];
    }
}

