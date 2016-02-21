<?php

namespace Night\Component\Templating;


interface Templating
{
    public function setVariable($variable, $value);
    public function setTemplate($template);
    public function render();
}

