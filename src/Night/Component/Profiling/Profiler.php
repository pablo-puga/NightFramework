<?php

namespace Night\Component\Profiling;


use Night\Component\Container\ServicesContainer;

class Profiler
{
    private static $state = false;
    /** @var Profiler */
    private static $instance = null;
    /** @var ServicesContainer */
    private $container;

    private function __construct() {}

    public static function enable()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$state = true;
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::enable();
        }
        return self::$instance;
    }

    public function getState()
    {
        return self::$state;
    }

    public function setContainer(ServicesContainer $container)
    {
        $this->container = $container;
    }

    public function printProfilerPanel()
    {
        $profilers = $this->container->getServicesByTag('profiler');

    }
}

