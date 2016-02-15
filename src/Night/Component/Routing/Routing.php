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

    public function parseRoute($route)
    {
        $fileContents = $this->fileParser->parse(file_get_contents($this->routingFile));
        foreach($fileContents as $routeEntry) {
            if ($routeEntry['route'] == $route) return $routeEntry['path'];
        }
        return false;
    }
}

