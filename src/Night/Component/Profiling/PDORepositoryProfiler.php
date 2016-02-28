<?php

namespace Night\Component\Profiling;


use ReflectionClass;

final class PDORepositoryProfiler extends ProfilerComponent
{
    private $traces = [];
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addTrace($statement, Array $variables, $result, $error, $executionTime)
    {
        $this->traces[] = [
            'statement' => $statement,
            'variables' => $variables,
            'result' => $result,
            'error' => $error,
            'executionTime' => $executionTime
        ];
    }

    public function getProfilingData()
    {
        if (empty($this->traces)) {
            return null;
        }
        $html = '<table><tr class="table-title"><th colspan="3" style="text-align: center">PDO REPOSITORY PROFILING INFORMATION</th></tr>';
        $html .= '<tr class="table-columns-definition"><th style="width: 55px;">Query Nb</th><th style="width: 70px;">Field</th><th>Value</th></tr>';
        $html .= '<tr class="empty"><td colspan="3"></td></tr>';
        foreach ($this->traces as $key => $trace) {
            $key = $key + 1;
            $traceHtml = "<tr><td>$key</td><td>Statement:</td><td>".$trace['statement']."</td></tr>";
            $variables = "";
            foreach($trace['variables'] as $variable => $value) {
                $variables .= "[$variable]: ".$value['value']." (".$this->getParamTypeNameByValue($value['type']).")<br>";
            }
            if (empty($variables)) $variables = 'None';
            $traceHtml .= "<tr><td>$key</td><td>    Arguments:</td><td>$variables</td></tr>";
            $traceHtml .= "<tr><td>$key</td><td>    Result:</td><td>".($trace['result'] ? 'OK' : 'FAILED')."</td></tr>";
            if (!$trace['result']) {
                $error = 'SQLESTATE: '.$trace['error'][0].'<br> MYSQLSTATE: '.$trace['error'][1].'<br> MSG: '.$trace['error'][2];
                $traceHtml .= "<tr><td>$key</td><td>    Errors:</td><td>$error</td></tr>";
            }
            $traceHtml .= "<tr><td>$key</td><td>    Exec Time:</td><td>".$trace['executionTime']."</td></tr>";
            $traceHtml .= '<tr class="empty"><td colspan="3"></td></tr>';
            $html .= $traceHtml;
        }
        return $html.'</table>';
    }

    private function getParamTypeNameByValue($value)
    {
        $class = new ReflectionClass('\Night\Component\Repository\PDORepository');
        $constants = array_flip($class->getConstants());
        return $constants[$value];
    }
}

