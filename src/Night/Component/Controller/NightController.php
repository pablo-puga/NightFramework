<?php

namespace Night\Component\Controller;


use Night\Component\Container\ServicesContainer;

abstract class NightController
{
    /** @var  ServicesContainer */
    private $servicesContainer;

    public function setServicesContainer(ServicesContainer $servicesContainer)
    {
        $this->servicesContainer = $servicesContainer;
    }

    public function getServicesContainer()
    {
        return clone $this->servicesContainer;
    }


}

