<?php

namespace Night\Component\Bootstrap;

use Night\Component\FileParser\PHPParser;
use Night\Component\FileParser\YAMLParser;
use Night\Component\FileParser\JSONParser;
use Night\Component\Request\Request;
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

    /**
     * @param \Night\Component\Request\Request $request
     * @return \Night\Component\Response\Response
     */
    public function __invoke(Request $request)
    {
        switch ($this->generalConfigurations['configurationsFileExtension']) {
            case PHPParser::FILE_EXTENSION:
                $fileParser = new PHPParser();
                break;
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

        $routeControllerInformation = $routing->parseRoute($request);

        $controllerClassName      = $routeControllerInformation->getClassName();
        $controllerCallableMethod = $routeControllerInformation->getCallableMethod();

        $controller = new $controllerClassName();
        $response = $controller->{$controllerCallableMethod}($request);

        return $response;
    }
}

