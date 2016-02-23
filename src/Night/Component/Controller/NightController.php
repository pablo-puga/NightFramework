<?php

namespace Night\Component\Controller;


use Night\Component\Templating\Templating;

abstract class NightController
{
    /** @var  Templating */
    private $templating;

    public function setTemplating(Templating $templating)
    {
        $this->templating = $templating;
    }

    public function getTemplatingService()
    {
        return clone $this->templating;
    }
}

