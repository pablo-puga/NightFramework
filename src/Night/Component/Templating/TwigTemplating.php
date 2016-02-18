<?php

namespace Night\Component\Templating;


class TwigTemplating implements Templating
{
    const ENGINE = 'twig';

    private $twig;
    private $variables = [];
    private $template;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function setVariable($variable, $value)
    {
        $this->variables[$variable] = $value;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function render()
    {
        return $this->twig->render($this->template, $this->variables);
    }
}

