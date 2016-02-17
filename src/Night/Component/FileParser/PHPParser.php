<?php

namespace Night\Component\FileParser;


class PHPParser implements FileParser
{
    const FILE_EXTENSION = 'php';

    public function parseFile($pathToFile)
    {
        $file = include($pathToFile);
        return $file;
    }
}

