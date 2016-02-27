<?php

namespace Night\Component\Profiling;


final class PDORepositoryProfiler extends ProfilerComponent
{
    private $traces = [];

    public function addTrace($statement, Array $variables, $result, $error)
    {
        $this->traces[] = [
            'statement' => $statement,
            'variables' => $variables,
            'result' => $result,
            'error' => $error
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
                $variables .= "[$variable]: ".$value['value']." (".$value['type'].")<br>";
            }
            $traceHtml .= "<tr><td>$key</td><td>    Arguments:</td><td>$variables</td></tr>";
            $traceHtml .= "<tr><td>$key</td><td>    Result:</td><td>".($trace['result'] ? 'OK' : 'FAILED')."</td></tr>";
            if (!$trace['result']) {
                $error = 'SQLESTATE: '.$trace['error'][0].'<br> MYSQLSTATE: '.$trace['error'][1].'<br> MSG: '.$trace['error'][2];
                $traceHtml .= "<tr><td>$key</td><td>    Errors:</td><td>$error</td></tr>";
            }
            $traceHtml .= '<tr class="empty"><td colspan="3"></td></tr>';
            $html .= $traceHtml;
        }
        return $html.'</table>';
    }
}

