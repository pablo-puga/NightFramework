<?php

namespace Night\Component\Bootstrap\Exception;


use Exception;
use InvalidArgumentException;
use Night\Component\Bootstrap\Bootstrap;

class InvalidEnvironment extends InvalidArgumentException
{
    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function throwDefault($environment)
    {
        $validEnvironments = [
            Bootstrap::PRODUCTION_ENVIRONMENT,
            Bootstrap::DEVELOPMENT_ENVIRONMENT
        ];
        throw new self("The environment selected [$environment] is not a valid framework environment. Must be one of [".implode(', ', $validEnvironments)."]", 1);
    }
}

