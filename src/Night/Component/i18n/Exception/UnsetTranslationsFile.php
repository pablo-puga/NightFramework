<?php

namespace Night\Component\i18n\Exception;


use Exception;
use InvalidArgumentException;

class UnsetTranslationsFile extends InvalidArgumentException
{
    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function throwDefault()
    {
        throw new self("The required file containing the translations is not set", 1);
    }
}

