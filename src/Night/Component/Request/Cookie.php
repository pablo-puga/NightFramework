<?php

namespace Night\Component\Request;


class Cookie
{
    private $globalCookie;

    public function __construct(Array $globalCookie)
    {
        $this->globalCookie = $globalCookie;
    }

    public function getParam($param)
    {
        return $this->globalCookie[$param];
    }
}

