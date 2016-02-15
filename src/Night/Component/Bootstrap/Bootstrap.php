<?php

namespace Night\Component\Bootstrap;

use Night\Component\Routing\Routing;
use Symfony\Component\Yaml\Yaml;

class Bootstrap
{
    const NIGHT_PRODUCTION_ENVIRONMENT = 'prod';
    const NIGHT_DEVELOPMENT_ENVIRONMENT = 'dev';

    private $currentEnvironment;
    private $configurationsDirectory;

    public function __construct($environment, $configurationsDirectory)
    {
        $this->currentEnvironment      = $environment;
        $this->configurationsDirectory = $configurationsDirectory;
    }

    public function __invoke($route)
    {
        $fileParser = new Yaml();
        $routing = new Routing($this->configurationsDirectory, $fileParser);
        $controllerInfo = $routing->parseRoute($route);

        $controller = new $controllerInfo['namespace']();
        $controller->{$controllerInfo['method']}();
    }
}

