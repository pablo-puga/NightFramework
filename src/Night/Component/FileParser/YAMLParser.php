<?php

namespace Night\Component\FileParser;


class YAMLParser implements FileParser
{
    const FILE_EXTENSION = 'yml';

    private $yaml;

    public function __construct($yamlParser)
    {
        $this->yaml = $yamlParser;
    }

    public function parseFile($pathToFile)
    {
        $fileContents = file_get_contents($pathToFile);
        $parsedFile   = $this->yaml->parse($fileContents);

        return $parsedFile;
    }
}

