<?php

namespace Night\Component\i18n;


use Night\Component\FileParser\FileParser;
use Night\Component\i18n\Exception\UnknownTranslation;
use Night\Component\i18n\Exception\UnsetTranslationsFile;

class Translator
{
    private $fileParser;
    private $translations = null;

    public function __construct(FileParser $fileParser)
    {
        $this->fileParser = $fileParser;
    }

    public function setTranslationsFile($translationsFile)
    {
        $this->translations = $this->fileParser->parseFile($translationsFile);
    }

    public function translate($message)
    {
        if (is_null($this->translations)) {
            UnsetTranslationsFile::throwDefault();
        }
        if (!array_key_exists($message, $this->translations)) {
            UnknownTranslation::throwDefault($message);
        }
        return $this->translations[$message];
    }
}

