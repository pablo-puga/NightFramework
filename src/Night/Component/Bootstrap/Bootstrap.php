<?php

namespace Night\Component\Bootstrap;


use Night\Component\Routing\Routing;

class Bootstrap
{
    const NIGHT_PRODUCTION_ENVIRONMENT = 'prod';
    const NIGHT_DEVELOPMENT_ENVIRONMENT = 'dev';

    private $currentEnvironment;
    private $configurationsDirectory;
    private $fileParser;

    public function __construct($environment, $configurationsDirectory, $fileParser)
    {
        $this->currentEnvironment      = $environment;
        $this->configurationsDirectory = $configurationsDirectory;
        $this->fileParser              = $fileParser;
    }

    public function __invoke($route)
    {
        $routing = new Routing($this->configurationsDirectory, $this->fileParser);
        $controllerInfo = $routing->parseRoute($route);

        $controller = new $controllerInfo['namespace']();
        $controller->{$controllerInfo['method']}();
    }
}

