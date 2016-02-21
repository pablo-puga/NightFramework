<?php

namespace Night\Component\FileParser;


use Night\Component\FileParser\Exception\InvalidConfigurationFileExtension;
use Symfony\Component\Yaml\Yaml;

class FileParserFactory
{
    public static function getParser($fileExtension)
    {
        switch ($fileExtension) {
            case PHPParser::FILE_EXTENSION:
                $fileParser = new PHPParser();
                break;
            case JSONParser::FILE_EXTENSION:
                $fileParser = new JSONParser();
                break;
            case YAMLParser::FILE_EXTENSION:
                $fileParser = new YAMLParser(new Yaml());
                break;
            default:
                InvalidConfigurationFileExtension::throwDefault($fileExtension);
        }

        return $fileParser;
    }
}

