<?php

namespace Night\Component\Routing;

use Night\Component\FileParser\FileParser;
use Night\Component\Request\Request;

class Routing
{
    private $routingFile;
    private $fileParser;

    public function __construct($configurationsDirectory, $configurationsFileExtension, FileParser $fileParser)
    {
        $this->routingFile = $configurationsDirectory . '/routing.' . $configurationsFileExtension;
        $this->fileParser  = $fileParser;
    }

    public function parseRoute(Request $request)
    {
        $fileContents = $this->fileParser->parseFile($this->routingFile);
        foreach ($fileContents as $routeEntry) {
            if ($routeEntry['route'] == $request->getRequestUri()) {
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

