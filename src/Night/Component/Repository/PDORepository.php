<?php

namespace Night\Component\Repository;


use Night\Component\Bootstrap\Bootstrap;
use Night\Component\FileParser\FileParser;
use Night\Component\Profiling\PDORepositoryProfiler;
use Night\Component\Profiling\Profiler;
use PDO;

class PDORepository
{
    const PARAM_STR = PDO::PARAM_STR;
    const PARAM_INT = PDO::PARAM_INT;
    const PARAM_BOOLEAN = PDO::PARAM_BOOL;

    /** @var \PDO */
    private $pdo;
    /** @var  \PDOStatement */
    private $currentQuery;
    private $currentStatement;
    private $currentParams = [];

    public function __construct(FileParser $fileParser)
    {
        $generalConfigurationFile = '../' . Bootstrap::CONFIGURATIONS_DIRECTORY . '/general.yml';
        $pdoConnectionSettings    = $fileParser->parseFile($generalConfigurationFile)['database']['pdo'];
        $host                     = $pdoConnectionSettings['host'];
        $database                 = $pdoConnectionSettings['database'];
        $user                     = $pdoConnectionSettings['user'];
        $password                 = $pdoConnectionSettings['password'];
        $charset                  = $pdoConnectionSettings['charset'];
        $this->pdo                = new PDO("mysql:host={$host};dbname={$database};charset={$charset}", $user, $password);
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function endTransaction()
    {
        $this->pdo->commit();
    }

    public function discardTransaction()
    {
        $this->pdo->rollBack();
    }

    public function setStatement($statemet)
    {
        $this->currentStatement = $statemet;
        $this->currentParams    = [];
    }

    public function setParam($param, $value, $type)
    {
        $this->currentParams[$param] = [
            'value' => $value,
            'type' => $type
        ];
    }

    public function executeStatement()
    {
        $this->currentQuery = $this->pdo->prepare($this->currentStatement);
        foreach ($this->currentParams as $param => $info) {
            $this->currentQuery->bindParam($param, $info['value'], $info['type']);
        }
        $initTime = microtime();
        $result   = $this->currentQuery->execute();
        $endTime  = microtime();

        if (Profiler::getState()) {
            /** @var PDORepositoryProfiler $profiler */
            $profiler = PDORepositoryProfiler::getInstance();
            $execTime = $this->calcQueryExecutionDuration($initTime, $endTime);
            $profiler->addTrace($this->currentStatement, $this->currentParams, $result, $this->getErrorInfo(), $execTime);
        }
        return $result;
    }

    public function getResults()
    {
        return $this->currentQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastInsertedId()
    {
        return $this->pdo->lastInsertId();
    }

    public function getErrorInfo()
    {
        return $this->currentQuery->errorInfo();
    }

    private function calcQueryExecutionDuration($initTime, $endTime)
    {
        $diffSeconds = $endTime - $initTime;
        $intPart     = floor($diffSeconds);
        if ($intPart == 0) {
            $diffMiliseconds = $diffSeconds * 1000;
            $intPart         = floor($diffMiliseconds);
            if ($intPart == 0) {
                $diffMicroseconds = $diffMiliseconds * 1000;
                $execDuration     = round($diffMicroseconds, 5, PHP_ROUND_HALF_UP) . " &#181;s";
            } else {
                $execDuration = round($diffMiliseconds, 5, PHP_ROUND_HALF_UP) . " ms";
            }
        } else {
            $execDuration = round($diffSeconds, 5, PHP_ROUND_HALF_UP) . " s";
        }
        return $execDuration;
    }
}

