<?php

namespace Night\Component\Templating;


use Night\Component\Bootstrap\Bootstrap;
use Night\Component\FileParser\FileParser;
use Night\Component\Profiling\Profiler;
use Night\Component\Profiling\TemplatingProfiler;
use Smarty;

class SmartyTemplating implements Templating
{
    const ENGINE = 'smarty';

    private $smarty;
    private $template;
    private $variables;

    public function __construct(FileParser $fileParser)
    {
        $generalConfigurationFile = '../' . Bootstrap::CONFIGURATIONS_DIRECTORY . '/general.yml';
        $smartySettings           = $fileParser->parseFile($generalConfigurationFile)['templating']['smarty'];
        $smarty                   = new Smarty();
        $smarty->setTemplateDir($smartySettings['templates']['directory']);
        if (isset($smartySettings['compilation']['directory']) && !empty($smartySettings['compilation']['directory'])) {
            $smarty->setCompileDir($smartySettings['compilation']['directory']);
        }
        if ($smartySettings['cache']['enable']) {
            $smarty->setCacheDir($smartySettings['cache']['directory']);
            $smarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
            $smarty->setCacheLifetime($smartySettings['cache']['lifeTime']);
        }
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
        if (Profiler::getState()) {
            $templatingProfiler = TemplatingProfiler::getInstance();
            $templatingProfiler->setRenderingInformation(
                self::ENGINE,
                $this->smarty->getTemplateDir()[0],
                $this->template,
                $this->variables,
                ($this->smarty->caching == Smarty::CACHING_OFF ? false : true),
                $this->smarty->getCacheDir(),
                $this->smarty->isCached($this->template)
            );
        }
        return $template;
    }
}

