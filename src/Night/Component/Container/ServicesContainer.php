<?php

namespace Night\Component\Container;


use Night\Component\Bootstrap\Bootstrap;
use Night\Component\FileParser\FileParser;

class ServicesContainer
{
    private $servicesDefinitions;

    public function __construct(FileParser $fileParser)
    {
        $coreServicesFile = __DIR__ . '/../../Configurations/services.yml';
        $userGeneralServicesFile     = '../' . Bootstrap::CONFIGURATIONS_DIRECTORY . '/services.yml';
        $userEnvironmentServicesFile = '../' . Bootstrap::CONFIGURATIONS_DIRECTORY . '/services_' . Bootstrap::$environment . '.yml';

        $this->servicesDefinitions      = $fileParser->parseFile($coreServicesFile);
        $userGeneralServicesDefinitions = $fileParser->parseFile($userGeneralServicesFile);
        if (empty($userGeneralServicesDefinitions)) {
            return;
        }
        foreach ($userGeneralServicesDefinitions as $service => $definition) {
            $this->servicesDefinitions[$service] = $definition;
        }
        if (file_exists($userEnvironmentServicesFile)) {
            $userEnvironmentServicesDefinitions = $fileParser->parseFile($userEnvironmentServicesFile);
            if (empty($userEnvironmentServicesDefinitions)) {
                return;
            }
            foreach ($userEnvironmentServicesDefinitions as $service => $definition) {
                $this->servicesDefinitions[$service] = $definition;
            }
        }
    }

    public function getService($serviceName)
    {
        $serviceDefinition = $this->servicesDefinitions[$serviceName];
        if (array_key_exists('public', $serviceDefinition)) {
            if (!$serviceDefinition['public'] && !$this->isCalledFromGetServiceMethod(__FUNCTION__)) {
                return null;
            }
        }
        if (array_key_exists('singleton', $serviceDefinition)) {
            return $serviceDefinition['class']::{$serviceDefinition['singleton']}();
        }
        if (array_key_exists('arguments', $serviceDefinition)) {
            $serviceArguments = [];
            foreach ($serviceDefinition['arguments'] as $argument) {
                if (strpos($argument, '@') !== 0) {
                    $serviceArguments[] = $argument;
                    continue;
                }
                $serviceArguments[] = $this->getService(str_replace('@', '', $argument));
            }
            $serviceReflector = new \ReflectionClass($serviceDefinition['class']);
            $service          = $serviceReflector->newInstanceArgs($serviceArguments);
        } else {
            $serviceReflector = new \ReflectionClass($serviceDefinition['class']);
            $service          = $serviceReflector->newInstance();
        }
        return $service;
    }

    private function isCalledFromGetServiceMethod($methodName)
    {
        $backTrace = debug_backtrace();

        $caller = null;
        if (count($backTrace) > 3) {
            $backTrace = $backTrace[2];
            $caller    = $backTrace['function'];
        }

        if (is_null($caller)) {
            return false;
        }

        if ($caller != $methodName) {
            return false;
        }

        return true;
    }

    public function getServicesByTag($tagName)
    {
        $tagServices = [];
        foreach ($this->servicesDefinitions as $service => $serviceDefinition) {
            if (array_key_exists('tags', $serviceDefinition)) {
                if (array_search($tagName, $serviceDefinition['tags']) >= 0) {
                    $tagServices[] = $this->getService($service);
                }
            }
        }
        return $tagServices;
    }
}

