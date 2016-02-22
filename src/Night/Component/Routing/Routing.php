<?php

namespace Night\Component\Routing;

use Night\Component\FileParser\FileParser;
use Night\Component\Request\Request;
use Night\Component\Request\Route;

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
        $fileContents    = $this->fileParser->parseFile($this->routingFile);
        $routeParameters = [];
        $explodedRoute   = array_slice(explode('/', $request->getRequestUri()), 1);

        foreach ($fileContents as $routeEntry) {
            $routeDefinition    = $routeEntry['route'];
            $explodedDefinition = array_slice(explode('/', $routeDefinition), 1);
            if (count($explodedRoute) != count($explodedDefinition)) {
                continue;
            }
            if ($explodedRoute[0] != $explodedDefinition[0]) {
                continue;
            }
            $isParam = false;
            for ($elementIterator = 0; $elementIterator < count($explodedDefinition); $elementIterator++) {
                if ($this->definitionElementIsParameter($explodedDefinition[$elementIterator]) || $isParam) {
                    $isParam                     = true;
                    $paramName                   = str_replace(['{', '}'], '', $explodedDefinition[$elementIterator]);
                    $routeParameters[$paramName] = $explodedRoute[$elementIterator];
                } else {
                    if ($explodedDefinition[$elementIterator] != $explodedRoute[$elementIterator]) {
                        break;
                    }
                }
                if ($elementIterator == count($explodedDefinition) - 1) {
                    $request->route             = new Route($routeDefinition, $routeParameters);
                    $className                  = $routeEntry['path']['classname'];
                    $callableMethod             = $routeEntry['path']['callablemethod'];
                    $routeControllerInformation = new RouteControllerInformation($className, $callableMethod);
                    return $routeControllerInformation;
                }
            }
        }

        $notFoundClassName                  = $fileContents['notfound']['path']['classname'];
        $notFoundCallableMethod             = $fileContents['notfound']['path']['callablemethod'];
        $notFoundRouteControllerInformation = new RouteControllerInformation($notFoundClassName,
            $notFoundCallableMethod);

        return $notFoundRouteControllerInformation;
    }

    private function definitionElementIsParameter($element)
    {
        return (strpos($element, '{') !== false && strpos($element, '}') !== false);
    }
}

