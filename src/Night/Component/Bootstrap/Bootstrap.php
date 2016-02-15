<?php

namespace Night\Component\Bootstrap;

use Night\Component\Routing\RouteControllerInformation;
use Night\Component\Routing\Routing;
use Symfony\Component\Yaml\Yaml;

class Bootstrap
{
    const NIGHT_PRODUCTION_ENVIRONMENT = 'prod';
    const NIGHT_DEVELOPMENT_ENVIRONMENT = 'dev';
    const NIGHT_YAML_FILE_EXTENSION = 'yml';
    const NIGHT_PHP_FILE_EXTENSION = 'php';
    const NIGHT_JSON_FILE_EXTENSION = 'json';

    private $generalConfigurations;

    public function __construct(Array $generalConfigurations)
    {
        $this->generalConfigurations = $generalConfigurations;
    }

    public function __invoke($route)
    {
        $fileParser = new Yaml();
        $routing    = new Routing($this->generalConfigurations['configurationsDirectory'], $fileParser);

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

