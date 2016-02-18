<?php

namespace Night\Component\Templating;


interface Templating
{
    public function setVariable($variable, $value);
    public function render();
}

