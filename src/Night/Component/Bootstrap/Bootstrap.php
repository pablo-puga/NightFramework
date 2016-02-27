<?php

namespace Night\Component\Bootstrap;

use Night\Component\Bootstrap\Exception\InvalidEnvironment;
use Night\Component\Bootstrap\Exception\InvalidResponse;
use Night\Component\Container\ServicesContainer;
use Night\Component\Controller\NightController;
use Night\Component\FileParser\FileParserFactory;
use Night\Component\FileParser\YAMLParser;
use Night\Component\Profiling\Profiler;
use Night\Component\Request\Request;
use Night\Component\Response\Response;
use Night\Component\Routing\RouteControllerInformation;
use ReflectionClass;

class Bootstrap
{
    private $generalConfigurations;
    private $container;
    private $configurationsDirectoryPath;
    public static $environment;

    const REQUEST_PARAM_NAME = 'request';
    const REQUEST_CLASS_NAME = 'Night\Component\Request\Request';
    const CONFIGURATIONS_DIRECTORY = 'app/confs';
    const PRODUCTION_ENVIRONMENT = 'prod';
    const DEVELOPMENT_ENVIRONMENT = 'dev';

    public function __construct()
    {
        $this->configurationsDirectoryPath = '../' . self::CONFIGURATIONS_DIRECTORY;
        $fileParser                        = FileParserFactory::getParser(YAMLParser::FILE_EXTENSION);
        $generalConfigurationFile          = $this->configurationsDirectoryPath . '/general.yml';
        $this->generalConfigurations       = $fileParser->parseFile($generalConfigurationFile)['general'];
        $environment = $this->generalConfigurations['environment'];
        if ($this->environmentIsValid($environment)) {
            InvalidEnvironment::throwDefault($environment);
        }
        self::$environment = $environment;
    }

    public function __invoke(Request $request)
    {
        $initTime          = microtime(true);
        $servicesFile      = $this->configurationsDirectoryPath . '/services.yml';
        $fileParser        = FileParserFactory::getParser(YAMLParser::FILE_EXTENSION);
        $servicesContainer = new ServicesContainer($fileParser, $servicesFile);
        $this->container   = $servicesContainer;
        if (Profiler::getState()) {
            Profiler::getInstance()->setContainer($this->container);
        }

        $routingFile = $this->configurationsDirectoryPath . '/routing.' . $this->generalConfigurations['routingFileExtension'];
        $routing     = $this->container->getService('routing');
        /** @var RouteControllerInformation $routeControllerInformation */
        $routeControllerInformation = $routing->parseRoute($request, $routingFile);

        $controllerClassName      = $routeControllerInformation->getClassName();
        $controllerCallableMethod = $routeControllerInformation->getCallableMethod();

        $controller = new $controllerClassName();

        if ($this->controllerIsChildOfNightController($controller)) {
            /** @var NightController $controller */
            $controller->setServicesContainer($servicesContainer);
        }

        /** @var Response $response */
        if ($this->controllerNeedsRequest($controllerClassName, $controllerCallableMethod)) {
            $response = $controller->{$controllerCallableMethod}($request);
        } else {
            $response = $controller->{$controllerCallableMethod}();
        }

        if (is_null($response)) {
            InvalidResponse::throwDefault();
        }
        $endTime = microtime(true);
        if (Profiler::getState()) {
            $executionTime = $this->calcExecutionDuration($initTime, $endTime);
            Profiler::getInstance()->setResponseStatus($response->getStatus());
            Profiler::getInstance()->setExecutionDuration($executionTime);
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

    private function calcExecutionDuration($initTime, $endTime)
    {
        $diffSeconds = $endTime - $initTime;
        $intPart     = floor($diffSeconds);
        if ($intPart == 0) {
            $diffMiliseconds = $diffSeconds * 1000;
            $intPart         = floor($diffMiliseconds);
            if ($intPart == 0) {
                $diffMicroseconds = $diffMiliseconds * 1000;
                $execDuration     = round($diffMicroseconds, 5, PHP_ROUND_HALF_UP) . " µs";
            } else {
                $execDuration = round($diffMiliseconds, 5, PHP_ROUND_HALF_UP) . " ms";
            }
        } else {
            $execDuration = round($diffSeconds, 5, PHP_ROUND_HALF_UP) . " s";
        }
        return $execDuration;
    }

    private function environmentIsValid($environment)
    {
        return $environment != self::PRODUCTION_ENVIRONMENT && $environment != self::DEVELOPMENT_ENVIRONMENT;
    }
}

