<?php

namespace Night\Component\Bootstrap;

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

    public function __construct(Array $generalConfigurations)
    {
        $this->generalConfigurations = $generalConfigurations;
    }

    public function __invoke(Request $request)
    {
        $fileParser = FileParserFactory::getParser($this->generalConfigurations['configurationsFileExtension']);

        $configurationsDirectory     = $this->generalConfigurations['configurationsDirectory'];
        $configurationsFileExtension = $this->generalConfigurations['configurationsFileExtension'];
        $routing                     = new Routing($configurationsDirectory, $configurationsFileExtension, $fileParser);

        $routeControllerInformation = $routing->parseRoute($request);

        $controllerClassName      = $routeControllerInformation->getClassName();
        $controllerCallableMethod = $routeControllerInformation->getCallableMethod();

        $controller = new $controllerClassName();

        if ($this->controllerIsChildOfNightController($controller)) {
            $this->setControllerServices($controller);
        }

        $response = $controller->{$controllerCallableMethod}($request);

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
                $twig_loader = new \Twig_Loader_Filesystem($this->generalConfigurations['templating']['templatesDirectory']);
                $twig        = new \Twig_Environment($twig_loader);
                $templating  = new TwigTemplating($twig);
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
}

