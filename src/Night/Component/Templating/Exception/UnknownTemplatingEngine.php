<?php

namespace Night\Component\Templating\Exception;


use Exception;
use InvalidArgumentException;

class UnknownTemplatingEngine extends InvalidArgumentException
{
    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function throwDefault($engine)
    {
        throw new self("Unknown templating engine [$engine]", 1);
    }
}

