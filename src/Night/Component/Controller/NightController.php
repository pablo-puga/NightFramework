<?php

namespace Night\Component\Controller;


use Night\Component\Templating\Templating;

abstract class NightController
{
    /** @var  Templating */
    protected $templating;

    public function setTemplating(Templating $templating)
    {
        $this->templating = $templating;
    }
}

