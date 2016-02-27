<?php

namespace Night\Component\FileParser\Exception;


use Exception;
use InvalidArgumentException;

class InvalidConfigurationFileExtension extends InvalidArgumentException
{
    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function throwDefault($fileExtension)
    {
        throw new self("The file extension [$fileExtension] is not a valid extension for configuration files", 1);
    }
}

