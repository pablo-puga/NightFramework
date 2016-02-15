<?php

namespace Night\Component\Bootstrap;


class Bootstrap
{
    const PRODUCTION_ENVIRONMENT = 'prod';
    const DEVELOPMENT_ENVIRONMENT = 'dev';

    private $currentEnvironment;
    private $configurationsDirectory;

    public function __construct($environment, $configurationsDirectory)
    {
        $this->currentEnvironment = $environment;
        $this->configurationsDirectory = $configurationsDirectory;
    }
}

