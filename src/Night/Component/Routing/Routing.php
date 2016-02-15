<?php

namespace Night\Component\Routing;


class Routing
{
    private $routingFile;
    private $fileParser;

    public function __construct($configurationsDirectory, $fileParser)
    {
        $this->routingFile = $configurationsDirectory . '/routing.yml';
        $this->fileParser  = $fileParser;
    }
}

