<?php

namespace Night\Component\Profiling;


abstract class ProfilerComponent
{
    protected static $instance = null;

    protected function __construct() {}
    protected function __clone() {}

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    abstract public function getProfilingData();
}

