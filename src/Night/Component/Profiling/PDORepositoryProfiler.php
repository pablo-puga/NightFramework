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
        $html = '<table><tr><th colspan="3">PDO REPOSITORY PROFILING INFORMATION</th></tr>';
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
            $traceHtml .= '<tr><td colspan="3" class="empty"></td></tr>';
            $html .= $traceHtml;
        }
        return $html.'</table>';
    }
}

