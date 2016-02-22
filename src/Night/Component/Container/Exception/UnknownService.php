<?php

namespace Night\Component\Container\Exception;


class UnknownService extends \InvalidArgumentException
{
    public function __construct($message, $code, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function throwDefault($service)
    {
        throw new self("Unknown service $service", 1);
    }
}

