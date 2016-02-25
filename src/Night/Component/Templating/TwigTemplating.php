<?php

namespace Night\Component\Templating;


use Night\Component\Bootstrap\Bootstrap;
use Night\Component\FileParser\FileParser;
use Twig_Environment;
use Twig_Loader_Filesystem;

class TwigTemplating implements Templating
{
    const ENGINE = 'twig';

    private $twig;
    private $variables = [];
    private $template;

    public function __construct(FileParser $fileParser)
    {
        $generalConfigurationFile = '../' . Bootstrap::CONFIGURATIONS_DIRECTORY . '/general.yml';
        $twigSettings             = $fileParser->parseFile($generalConfigurationFile)['templating']['twig'];
        $environmentSettings      = [];
        if ($twigSettings['debug']) {
            $environmentSettings['debug'] = true;
        }
        if (isset($twigSettings['cacheDirectory']) && !empty($twigSettings['cacheDirectory'])) {
            $environmentSettings['cache'] = $twigSettings['cacheDirectory'];
        }

        $twig_loader = new Twig_Loader_Filesystem($twigSettings['templatesDirectory']);
        $twig        = new Twig_Environment($twig_loader, $environmentSettings);
        $this->twig  = $twig;
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

