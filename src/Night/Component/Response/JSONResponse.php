<?php

namespace Night\Component\Response;


class JSONResponse extends Response
{
    private $charset;

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function setContent($responseContent)
    {
        parent::setContentType("application/json".((isset($this->charset) ? "; charset=$this->charset" : "")));
        $jsonContent = json_encode($responseContent);
        parent::setContent($jsonContent);
    }
}

