<?php

namespace Night\Component\Request;


use Night\Component\Request\Exception\InvalidRequestParam;

class Route
{
    private $matchedDefinition;
    private $parameters;

    public function __construct($matchedDefinition, Array $parameters)
    {
        $this->matchedDefinition = $matchedDefinition;
        $this->parameters        = $parameters;
    }

    public function getMatchedDefinition()
    {
        return $this->matchedDefinition;
    }

    public function getParam($param)
    {
        if (!array_key_exists($param, $this->parameters)) {
            InvalidRequestParam::throwDefault($param, get_class());
        }
        return $this->parameters[$param];
    }
}

