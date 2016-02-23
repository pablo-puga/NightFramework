<?php

namespace Night\Component\Bootstrap;

use Night\Component\Container\ServicesContainer;
use Night\Component\Controller\NightController;
use Night\Component\FileParser\FileParserFactory;
use Night\Component\FileParser\YAMLParser;
use Night\Component\Request\Request;
use Night\Component\Routing\RouteControllerInformation;
use NightStandardEdition\Controller\ComplexController;
use ReflectionClass;

class Bootstrap
{
    private $generalConfigurations;
    private $container;
    private $configurationsDirectoryPath;

    const REQUEST_PARAM_NAME = 'request';
    const REQUEST_CLASS_NAME = 'Night\Component\Request\Request';
    const CONFIGURATIONS_DIRECTORY = 'app/confs';

    public function __construct()
    {
        $this->configurationsDirectoryPath = '../' . self::CONFIGURATIONS_DIRECTORY;
        $fileParser                        = FileParserFactory::getParser(YAMLParser::FILE_EXTENSION);
        $generalConfigurationFile          = $this->configurationsDirectoryPath . '/general.yml';
        $this->generalConfigurations       = $fileParser->parseFile($generalConfigurationFile)['general'];
    }

    public function __invoke(Request $request)
    {

        $servicesFile      = $this->configurationsDirectoryPath . '/services.yml';
        $fileParser        = FileParserFactory::getParser(YAMLParser::FILE_EXTENSION);
        $servicesContainer = new ServicesContainer($fileParser, $servicesFile);
        $this->container   = $servicesContainer;

        $routingFile                = $this->configurationsDirectoryPath . '/routing.' . $this->generalConfigurations['routingFileExtension'];
        $routing                    = $this->container->getService('routing');
        /** @var RouteControllerInformation $routeControllerInformation */
        $routeControllerInformation = $routing->parseRoute($request, $routingFile);

        $controllerClassName      = $routeControllerInformation->getClassName();
        $controllerCallableMethod = $routeControllerInformation->getCallableMethod();

        $controller = new $controllerClassName();

        if ($this->controllerIsChildOfNightController($controller)) {
            /** @var NightController $controller */
            $controller->setServicesContainer($servicesContainer);
        }

        if ($this->controllerNeedsRequest($controllerClassName, $controllerCallableMethod)) {
            $response = $controller->{$controllerCallableMethod}($request);
        } else {
            $response = $controller->{$controllerCallableMethod}();
        }

        return $response;
    }

    private function controllerIsChildOfNightController($controller)
    {
        return is_subclass_of($controller, 'Night\Component\Controller\NightController');
    }

    private function controllerNeedsRequest($controllerClassName, $controllerCallableMethod)
    {
        $reflectionClass = new ReflectionClass($controllerClassName);

        foreach ($reflectionClass->getMethod($controllerCallableMethod)->getParameters() as $param) {
            if ($param->name == self::REQUEST_PARAM_NAME && $param->getClass()->name == self::REQUEST_CLASS_NAME) {
                return true;
            }
        }

        return false;
    }
}

