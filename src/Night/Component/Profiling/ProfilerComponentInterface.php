<?php

namespace Night\Component\Profiling;


interface ProfilerComponentInterface
{
    public static function getInstance();
    public function getProfilingData();
}

