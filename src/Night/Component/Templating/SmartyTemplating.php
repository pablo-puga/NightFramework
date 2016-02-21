<?php

namespace Night\Component\Templating;


class SmartyTemplating implements Templating
{
    const ENGINE = 'smarty';

    private $smarty;
    private $template;
    private $variables;

    public function __construct(\Smarty $smarty)
    {
        $this->smarty = $smarty;
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
        $this->smarty->assign($this->variables);
        $template = $this->smarty->fetch($this->template);
        return $template;
    }
}

