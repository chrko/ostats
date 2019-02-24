<?php

namespace ChrKo\OStats\BulkQuery;


use ChrKo\OStats\DB;

abstract class AbstractExecutor implements ExecutorInterface {
    /**
     * @var DB
     */
    protected $dbConn;
    /**
     * @var string
     */
    protected $query;
    /**
     * @var int
     */
    protected $counter = 0;
    /**
     * @var int
     */
    protected $batchSize = 2000;

    /**
     * AbstractExecutor constructor.
     * @param DB $dbConn
     */
    public function __construct(DB $dbConn) {
        $this->dbConn = $dbConn;
        $this->query = $this->getQueryStart();
    }

    public function run($data) {
        $this->query .= $this->dbConn->namedReplace($this->getQueryPart(), $data);
        $this->counter++;

        if ($this->counter % $this->batchSize == 0) {
            $this->flush();
        }

        return $this;
    }

    public function __destruct() {
        $this->finish();
    }

    public function finish() {
        if ($this->counter > 0 && $this->counter % $this->batchSize != 0) {
            $this->flush();
            $this->counter = 0;
        }

        return $this;
    }

    protected abstract function getQueryStart();

    protected abstract function getQueryPart();

    protected function flush() {
        $this->queryPartCut();
        $this->query .= $this->getQueryEnd();
        $this->query .= ';';
        $this->dbConn->query($this->query);
        $this->query = $this->getQueryStart();

        return $this;
    }

    protected function queryPartCut() {
        $this->query = substr($this->query, 0, -2);

        return $this;
    }

    protected abstract function getQueryEnd();
}
