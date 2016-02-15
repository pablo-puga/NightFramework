<?php

namespace Night\Component\Routing;

use Night\Component\FileParser\FileParser;

class Routing
{
    private $routingFile;
    private $fileParser;

    public function __construct($configurationsDirectory, FileParser $fileParser)
    {
        $this->routingFile = $configurationsDirectory . '/routing.yml';
        $this->fileParser  = $fileParser;
    }

    public function parseRoute($route)
    {
        $fileContents = $this->fileParser->parseFile($this->routingFile);
        foreach ($fileContents as $routeEntry) {
            if ($routeEntry['route'] == $route) {
                $className                  = $routeEntry['path']['classname'];
                $callableMethod             = $routeEntry['path']['callablemethod'];
                $routeControllerInformation = new RouteControllerInformation($className, $callableMethod);
                return $routeControllerInformation;
            }
        }
        $notFoundClassName                  = $fileContents['notfound']['path']['classname'];
        $notFoundCallableMethod             = $fileContents['notfound']['path']['callablemethod'];
        $notFoundRouteControllerInformation = new RouteControllerInformation($notFoundClassName, $notFoundCallableMethod);

        return $notFoundRouteControllerInformation;
    }
}

