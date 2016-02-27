<?php

namespace Night\Component\Profiling;


use Night\Component\Container\ServicesContainer;

class Profiler
{
    private static $state = false;
    /** @var Profiler */
    private static $instance = null;
    /** @var ServicesContainer */
    private $container;
    private $responseStatus;
    private $executionDuration;
    private $css = <<<__CSS__
<style>
.profiler-panel { background-color: #153039; border: 1px solid #153039; border-bottom: none; border-top-left-radius: 10px; border-top-right-radius: 10px; bottom: 0; color: white; font-family: "Trebuchet MS", Helvetica, sans-serif; font-size: 16px; height: 30px; left: 50%; position: fixed; transform: translate(-50%); transition: height 0.5s; width: 95%; }
.profiler-panel-response-status { display: inline-block; float: right; margin-right: 10px; margin-top: 5px; }
.profiler-panel-execution-duration { display: inline-block; float: left; margin-left: 10px; margin-top: 5px; }
.profiler-panel-expander { background-color: #a2ea7d; border: 1px solid #a2ea7d; border-radius: 100%; color: #153039; cursor: pointer; display: inline-block; float: right; font-size: 20px; height: 15px; line-height: 15px; margin-right: 5px; margin-top: 5px; text-align: center; width: 15px; }
.profiler-panel-expander:hover { background-color: #49d2cb; border-color: #49d2cb; }
.profiler-panel-components-holder { height: calc(100% - 45px); margin: 35px auto 10px; opacity: 0; overflow-y: auto; transition: opacity 2s; width: 95%; }
.profiler-panel-components-holder table { font-size: 12px; text-align: left; width: 100%; }
.profiler-panel-components-holder table tr { background-color: #F2F2F2; color: #153039; vertical-align: top; }
.profiler-panel-components-holder table td, .profiler-panel-components-holder table th { border-collapse: collapse; padding: 5px; white-space: normal; word-break: break-all; word-wrap: break-word; }
.profiler-panel-components-holder table tr.empty { background-color: transparent; }
.profiler-panel-components-holder table tr.empty td { background-color: transparent; padding: 1px; }
.profiler-panel-components-holder table .table-title { background-color: #848484; color: white; }
.profiler-panel-components-holder table .table-columns-definition { background-color: #848484; color: white; }
</style>
__CSS__;
    private $javascript = <<<__JS__
<script type="text/javascript">
document.querySelector('.profiler-panel-expander').addEventListener('click', function (event) {
    var innerHTML = event.target.innerHTML;
    if (innerHTML == '+') {
        event.target.innerHTML = '-';
        document.querySelector('.profiler-panel').style.height = '95%';
        document.querySelector('.profiler-panel-components-holder').style.opacity = '1';
    } else if (innerHTML == '-') {
        event.target.innerHTML = '+';
        document.querySelector('.profiler-panel').style.height = '30px';
        document.querySelector('.profiler-panel-components-holder').style.opacity = '0';
    }
});
</script>
__JS__;


    private function __construct() {}

    public static function enable()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$state    = true;
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::enable();
        }
        return self::$instance;
    }

    public static function getState()
    {
        return self::$state;
    }

    public function setContainer(ServicesContainer $container)
    {
        $this->container = $container;
    }

    public function setExecutionDuration($executionDuration)
    {
        $this->executionDuration = $executionDuration;
    }

    public function setResponseStatus($responseStatus)
    {
        $this->responseStatus = $responseStatus;
    }

    public function getProfilerHTMLPanel()
    {
        /** @var ProfilerComponent[] $profilers */
        $profilers      = $this->container->getServicesByTag('profiler-component');
        $html           = $this->css;
        $responseStatus = is_null($this->responseStatus) ? 'Not Set' : $this->responseStatus;
        $html .= <<< __HTML__
<div class="profiler-panel">
    <div class="profiler-panel-execution-duration">Execution duration: $this->executionDuration</div>
    <div class="profiler-panel-expander">+</div>
    <div class="profiler-panel-response-status">Response Status: $responseStatus</div>
    <div class="profiler-panel-components-holder">
__HTML__;
        foreach ($profilers as $profilerComponent) {
            $profilingData = $profilerComponent->getProfilingData();
            if (!is_null($profilingData)) {
                $html .= $profilingData;
            }
        }
        $html .= '</div></div>';
        $html .= $this->javascript;
        return $html;
    }
}

