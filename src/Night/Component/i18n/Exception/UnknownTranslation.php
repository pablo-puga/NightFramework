<?php

namespace Night\Component\i18n\Exception;


use Exception;
use InvalidArgumentException;

class UnknownTranslation extends InvalidArgumentException
{
    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function throwDefault($translation)
    {
        throw new self("There is not an available translation for [$translation]", 1);
    }
}

