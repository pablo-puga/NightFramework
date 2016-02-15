<?php

namespace Night\Component\Routing;


class RouteControllerInformation
{
    private $className;
    private $callableMethod;

    public function __construct($className, $callableMethod)
    {
        $this->className      = $className;
        $this->callableMethod = $callableMethod;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getCallableMethod()
    {
        return $this->callableMethod;
    }
}

