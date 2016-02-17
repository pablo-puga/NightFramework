<?php

namespace Night\Component\Request;


class Get
{
    private $globalGet;

    public function __construct(Array $globalGet)
    {
        $this->globalGet = $globalGet;
    }

    public function getParam($param)
    {
        return $this->globalGet[$param];
    }
}

