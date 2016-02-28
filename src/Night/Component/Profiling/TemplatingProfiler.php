<?php

namespace Night\Component\Profiling;


class TemplatingProfiler extends ProfilerComponent
{
    private $renderingInformation = [];
    private static $instance = null;

    /**
     * @return \Night\Component\Profiling\TemplatingProfiler
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setRenderingInformation(
        $engine,
        $templatesDir,
        $template,
        $params,
        $isCacheEnabled,
        $cacheDir = null,
        $isInCache = null)
    {
        $this->renderingInformation[] = [
            'engine' => $engine,
            'templatesDir' => realpath($templatesDir),
            'template' => $template,
            'params' => $params,
            'isCacheEnabled' => $isCacheEnabled,
            'cacheDir' => realpath($cacheDir),
            'isInCache' => $isInCache
        ];
    }

    public function getProfilingData()
    {
        if (empty($this->renderingInformation)) {
            return null;
        }
        $html = '<table><tr class="table-title"><th colspan="3" style="text-align: center">TEMPLATING INFORMATION</th></tr>';
        $html .= '<tr class="table-columns-definition"><th style="width: 75px;">Template Nb</th><th style="width: 125px;">Field</th><th>Value</th></tr>';
        $html .= '<tr class="empty"><td colspan="3"></td></tr>';
        foreach ($this->renderingInformation as $tplNb => $templateInfo) {
            $tplNb = $tplNb + 1;
            $html .= "<tr><td>$tplNb</td><td>Engine:</td><td>".$templateInfo['engine']."</td></tr>";
            $html .= "<tr><td>$tplNb</td><td>Templates Directory:</td><td>".$templateInfo['templatesDir']."</td></tr>";
            $html .= "<tr><td>$tplNb</td><td>Rendered Template:</td><td>".$templateInfo['template']."</td></tr>";
            $params = "";
            foreach($templateInfo['params'] as $param => $value) {
                $params .= "[$param]: $value<br>";
            }
            if (empty($params)) $params = 'None';
            $html .= "<tr><td>$tplNb</td><td>Parameters:</td><td>$params</td></tr>";
            $html .= "<tr><td>$tplNb</td><td>Cache:</td><td>".($templateInfo['isCacheEnabled'] ? 'Enabled' : 'Disabled')."</td></tr>";
            if ($templateInfo['isCacheEnabled']) {
                $html .= "<tr><td>$tplNb</td><td>Cache Directory:</td><td>".$templateInfo['cacheDir']."</td></tr>";
                $html .= "<tr><td>$tplNb</td><td>Is template in cache:</td><td>".($templateInfo['isInCache'] ? 'Yes' : 'No')."</td></tr>";
            }
            $html .= '<tr class="empty"><td colspan="3"></td></tr>';
        }
        $html .= '</table>';
        return $html;
    }
}

