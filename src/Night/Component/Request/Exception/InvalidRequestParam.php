<?php

namespace Night\Component\Request;


class InvalidRequestParam extends \InvalidArgumentException
{
    public function __construct($message, $code, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function throwDefault($param, $class)
    {
        $message = "The param [$param] that you are trying to obtain doesn't exist in $class";
        throw new self($message, 1);
    }
}

