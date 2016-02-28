<?php

namespace Night\Component\Templating;


use Night\Component\Bootstrap\Bootstrap;
use Night\Component\FileParser\FileParser;
use Night\Component\Profiling\Profiler;
use Night\Component\Profiling\TemplatingProfiler;
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
        if (Profiler::getState()) {
            $templatingProfiler = TemplatingProfiler::getInstance();
            $templatingProfiler->setRenderingInformation(
                self::ENGINE,
                $this->twig->getLoader()->getPaths()[0],
                $this->template,
                $this->variables,
                ($this->twig->getCache() ? true : false),
                $this->twig->getCache(),
                ($this->twig->getCacheFilename($this->template) ? true : false)
            );
        }
        return $this->twig->render($this->template, $this->variables);
    }
}

