<?php

namespace Night\Component\Profiling;


use Night\Component\Request\Route;

class RoutingProfiler extends ProfilerComponent
{
    private $requestedUri;
    /** @var  Route */
    private $matchedDefinition;
    private $parsingTime;
    private $routeArguments;
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setInformation($requestedUri, $matchedDefinition, Array $routeArguments, $parsingTime)
    {
        $this->requestedUri      = $requestedUri;
        $this->matchedDefinition = $matchedDefinition;
        $this->parsingTime       = $parsingTime;
        $this->routeArguments    = $routeArguments;
    }

    public function getProfilingData()
    {
        $parameters = "";
        foreach ($this->routeArguments as $routeArgument => $value) {
            $parameters .= "[$routeArgument]: $value<br>";
        }
        if (empty($parameters)) $parameters = 'None';
        $html = <<<__HTML__
<table>
    <tr class="table-title"><th colspan="2" style="text-align: center">ROUTING PROFILING INFORMATION</th></tr>
    <tr class="empty"><td colspan="2"></td></tr>
    <tr><td style="width: 100px;">Requested URI:</td><td>$this->requestedUri</td></tr>
    <tr><td style="width: 100px;">Matched Route:</td><td>$this->matchedDefinition</td></tr>
    <tr><td style="width: 100px;">Parameters: </td><td>$parameters</td></tr>
    <tr><td style="width: 100px;">Parsing Duration: </td><td>$this->parsingTime</td>
</table>
__HTML__;
        return $html;
    }
}

