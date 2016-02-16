<?php

namespace Night\Component\Bootstrap;

use Night\Component\FileParser\YAMLParser;
use Night\Component\FileParser\JSONParser;
use Night\Component\Routing\RouteControllerInformation;
use Night\Component\Routing\Routing;
use Symfony\Component\Yaml\Yaml;

class Bootstrap
{
    const NIGHT_PRODUCTION_ENVIRONMENT = 'prod';
    const NIGHT_DEVELOPMENT_ENVIRONMENT = 'dev';
    
    private $generalConfigurations;

    public function __construct(Array $generalConfigurations)
    {
        $this->generalConfigurations = $generalConfigurations;
    }

    public function __invoke($route)
    {
        switch ($this->generalConfigurations['configurationsFileExtension']) {
            case JSONParser::FILE_EXTENSION:
                $fileParser = new JSONParser();
                break;
            case YAMLParser::FILE_EXTENSION:
            default:
                $fileParser = new YAMLParser(new Yaml());
        }

        $configurationsDirectory     = $this->generalConfigurations['configurationsDirectory'];
        $configurationsFileExtension = $this->generalConfigurations['configurationsFileExtension'];
        $routing                     = new Routing($configurationsDirectory, $configurationsFileExtension, $fileParser);

        $routeControllerInformation = $routing->parseRoute($route);
        $this->invokeController($routeControllerInformation);
    }

    private function invokeController(RouteControllerInformation $routeControllerInformation)
    {
        $controllerClassName      = $routeControllerInformation->getClassName();
        $controllerCallableMethod = $routeControllerInformation->getCallableMethod();

        $controller = new $controllerClassName();
        $controller->{$controllerCallableMethod}();
    }
}

