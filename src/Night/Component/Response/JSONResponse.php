<?php

namespace Night\Component\Response;


class JSONResponse extends Response
{
    public function __construct()
    {
        parent::setContentType('application/json');
    }

    public function setContent($responseContent)
    {
        $jsonContent = json_encode($responseContent);
        parent::setContent($jsonContent);
    }
}

