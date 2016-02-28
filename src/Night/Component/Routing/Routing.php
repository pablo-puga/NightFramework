<?php

namespace Night\Component\Routing;

use Night\Component\FileParser\FileParser;
use Night\Component\Profiling\Profiler;
use Night\Component\Profiling\RoutingProfiler;
use Night\Component\Request\Request;
use Night\Component\Request\Route;

class Routing
{
    private $fileParser;

    public function __construct(FileParser $fileParser)
    {
        $this->fileParser = $fileParser;
    }

    public function parseRoute(Request $request, $routingFile)
    {
        $initTime        = microtime();
        $fileContents    = $this->fileParser->parseFile($routingFile);
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
                    break 2;
                }
            }
        }

        if (!isset($routeControllerInformation)) {
            $notFoundClassName          = $fileContents['notfound']['path']['classname'];
            $notFoundCallableMethod     = $fileContents['notfound']['path']['callablemethod'];
            $routeControllerInformation = new RouteControllerInformation($notFoundClassName, $notFoundCallableMethod);
            $routeDefinition            = 'Default Not Found';
        }

        $endTime = microtime();
        if (Profiler::getState()) {
            $parsingDuration = $this->calcParsingDuration($initTime, $endTime);
            RoutingProfiler::getInstance()->setInformation($request->getRequestUri(), $routeDefinition, $routeParameters, $parsingDuration);
        }

        return $routeControllerInformation;
    }

    private function definitionElementIsParameter($element)
    {
        return (strpos($element, '{') !== false && strpos($element, '}') !== false);
    }

    private function calcParsingDuration($initTime, $endTime)
    {
        $diffSeconds = $endTime - $initTime;
        $intPart     = floor($diffSeconds);
        if ($intPart == 0) {
            $diffMiliseconds = $diffSeconds * 1000;
            $intPart         = floor($diffMiliseconds);
            if ($intPart == 0) {
                $diffMicroseconds = $diffMiliseconds * 1000;
                $execDuration     = round($diffMicroseconds, 5, PHP_ROUND_HALF_UP) . " &#181;s";
            } else {
                $execDuration = round($diffMiliseconds, 5, PHP_ROUND_HALF_UP) . " ms";
            }
        } else {
            $execDuration = round($diffSeconds, 5, PHP_ROUND_HALF_UP) . " s";
        }
        return $execDuration;
    }
}

