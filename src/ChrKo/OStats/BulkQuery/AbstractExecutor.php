<?php

namespace ChrKo\OStats\BulkQuery;


use ChrKo\OStats\DB;
use ChrKo\Prometheus;
use Prometheus\Histogram;

abstract class AbstractExecutor implements ExecutorInterface
{
    /**
     * @var Histogram
     */
    private static $runHistogram;

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
    public function __construct(DB $dbConn)
    {
        $this->dbConn = $dbConn;
        $this->query = $this->getQueryStart();
    }

    protected abstract function getQueryStart();

    public function run($data)
    {
        Prometheus::getStopwatch()->start('abstract_executor_' . $this->getType());
        $this->query .= $this->dbConn->namedReplace($this->getQueryPart(), $data);
        $this->counter++;

        if ($this->counter % $this->batchSize == 0) {
            $this->counter = 0;
            $this->flush();
        }
        $event = Prometheus::getStopwatch()->stop('abstract_executor_' . $this->getType());
        self::getRunHistogram()->observe(
            $event->getDuration() / 1000,
            [
                Prometheus::getProcessId(),
                $this->getType(),
            ]
        );

        return $this;
    }

    protected function getType(): string
    {
        return static::class;
    }

    protected abstract function getQueryPart();

    protected function flush()
    {
        $this->queryPartCut();
        $this->query .= $this->getQueryEnd();
        $this->query .= ';';
        $this->dbConn->query($this->query);
        $this->query = $this->getQueryStart();

        return $this;
    }

    protected function queryPartCut()
    {
        $this->query = substr($this->query, 0, -2);

        return $this;
    }

    protected abstract function getQueryEnd();

    public static function getRunHistogram(): Histogram
    {
        if (!self::$runHistogram) {
            return self::$runHistogram = Prometheus::getRegistry()
                ->registerHistogram(
                    'ostats',
                    'abstract_executer_run_seconds',
                    'Abstract Executer Timing',
                    [
                        'process_id',
                        'type',
                    ]
                );
        }
        return self::$runHistogram;
    }

    public function __destruct()
    {
        $this->finish();
    }

    public function finish()
    {
        if ($this->counter > 0 && $this->counter % $this->batchSize != 0) {
            $this->flush();
            $this->counter = 0;
        }

        return $this;
    }
}
