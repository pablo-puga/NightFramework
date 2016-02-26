<?php

namespace Night\Component\Repository;


use Night\Component\Bootstrap\Bootstrap;
use Night\Component\FileParser\FileParser;
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

    public function __construct(FileParser $fileParser)
    {
        $generalConfigurationFile = '../' . Bootstrap::CONFIGURATIONS_DIRECTORY . '/general.yml';
        $pdoConnectionSettings    = $fileParser->parseFile($generalConfigurationFile)['database']['pdo'];
        $host                     = $pdoConnectionSettings['host'];
        $database                 = $pdoConnectionSettings['database'];
        $user                     = $pdoConnectionSettings['user'];
        $password                 = $pdoConnectionSettings['password'];
        $this->pdo                = new PDO("mysql:host={$host};dbname={$database}", $user, $password);
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
        $this->currentQuery = $this->pdo->prepare($statemet);
    }

    public function setParam($param, $value, $type)
    {
        $this->currentQuery->bindParam($param, $value, $type);
    }

    public function executeStatement()
    {
        return $this->currentQuery->execute();
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
}

