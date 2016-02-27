<?php

namespace Night\Component\Bootstrap\Exception;


use MongoDB\Driver\Exception\InvalidArgumentException;

class InvalidResponse extends InvalidArgumentException
{
    public function __construct($message, $code, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function throwDefault()
    {
        throw new self('The response returned by the controller is not a valid \Night\Component\Response\Response', 1);
    }
}

