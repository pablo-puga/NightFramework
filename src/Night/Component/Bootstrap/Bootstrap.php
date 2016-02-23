<?php

namespace Night\Component\Bootstrap;

use Night\Component\Container\ServicesContainer;
use Night\Component\Controller\NightController;
use Night\Component\FileParser\FileParserFactory;
use Night\Component\Request\Request;
use Night\Component\Routing\Routing;
use Night\Component\Templating\Exception\UnknownTemplatingEngine;
use Night\Component\Templating\SmartyTemplating;
use Night\Component\Templating\TwigTemplating;

class Bootstrap
{
    const NIGHT_PRODUCTION_ENVIRONMENT = 'prod';
    const NIGHT_DEVELOPMENT_ENVIRONMENT = 'dev';

    private $generalConfigurations;
    private $container;

    const REQUEST_PARAM_NAME = 'request';

    const REQUEST_CLASS_NAME = 'Night\Component\Request\Request';

    public function __construct(Array $generalConfigurations)
    {
        $this->generalConfigurations = $generalConfigurations;
    }

    public function __invoke(Request $request)
    {
        $fileParser                  = FileParserFactory::getParser($this->generalConfigurations['configurationsFileExtension']);
        $configurationsDirectory     = $this->generalConfigurations['configurationsDirectory'];
        $configurationsFileExtension = $this->generalConfigurations['configurationsFileExtension'];

        $servicesFile      = $configurationsDirectory . '/services.' . $configurationsFileExtension;
        $servicesContainer = new ServicesContainer($fileParser, $servicesFile);
        $this->container   = $servicesContainer;

        $routingFile = $configurationsDirectory . '/routing.' . $configurationsFileExtension;
        $routing     = $servicesContainer->getService('routing');

        $routeControllerInformation = $routing->parseRoute($request, $routingFile);

        $controllerClassName      = $routeControllerInformation->getClassName();
        $controllerCallableMethod = $routeControllerInformation->getCallableMethod();

        $controller = new $controllerClassName();

        if ($this->controllerIsChildOfNightController($controller)) {
            $this->setControllerServices($controller);
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

    private function setControllerServices(NightController $controller)
    {
        switch ($this->generalConfigurations['templating']['engine']) {
            case TwigTemplating::ENGINE:
                $templating  = $this->container->getService('twig-templating');
                break;
            case SmartyTemplating::ENGINE:
                $smarty = new \Smarty();
                $smarty->setTemplateDir($this->generalConfigurations['templating']['templatesDirectory']);
                $templating = new SmartyTemplating($smarty);
                break;
            default:
                UnknownTemplatingEngine::throwDefault($this->generalConfigurations['templating']['engine']);
                break;
        }

        $controller->setTemplating($templating);

    }

    private function controllerNeedsRequest($controllerClassName, $controllerCallableMethod)
    {
        $reflectionClass = new \ReflectionClass($controllerClassName);

        foreach ($reflectionClass->getMethod($controllerCallableMethod)->getParameters() as $param) {
            if ($param->name == self::REQUEST_PARAM_NAME && $param->getClass()->name == self::REQUEST_CLASS_NAME) {
                return true;
            }
        }

        return false;
    }
}

