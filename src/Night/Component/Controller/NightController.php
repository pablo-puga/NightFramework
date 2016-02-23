<?php

namespace Night\Component\Controller;


use Night\Component\Container\ServicesContainer;
use Night\Component\Templating\Templating;

abstract class NightController
{
    /** @var  Templating */
    private $templating;
    /** @var  ServicesContainer */
    private $servicesContainer;

    public function setTemplating(Templating $templating)
    {
        $this->templating = $templating;
    }

    public function getTemplatingService()
    {
        return clone $this->templating;
    }

    public function setServicesContainer(ServicesContainer $servicesContainer)
    {
        $this->servicesContainer = $servicesContainer;
    }

    public function getServicesContainer()
    {
        return clone $this->servicesContainer;
    }


}

