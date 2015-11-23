<?php

namespace ChrKo;


abstract class Updater implements UpdaterInterface
{
    /**
     * @var \mysqli
     */
    protected $db;
    protected $query;
    protected $counter = 0;
    protected $size = 500;

    public function __construct()
    {
        $this->db = DB::getConn();
        $this->query = $this->getQueryStart();
    }

    protected abstract function getQueryStart();

    public function run($data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = $this->db->real_escape_string($value);
        }

        $this->query .= DB::namedReplace($this->getQueryPart(), $data);
        $this->counter++;

        if ($this->counter % $this->size == 0) {
            $this->flush();
        }
    }

    protected abstract function getQueryPart();

    protected function flush()
    {
        $this->query = substr($this->query, 0, strlen($this->query) - 2);
        $this->query .= $this->getQueryEnd();
        $this->query .= ';';
        $this->db->query($this->query);
        $this->query = $this->getQueryStart();
    }

    protected abstract function getQueryEnd();

    public function finish()
    {
        if ($this->counter % $this->size != 0) {
            $this->flush();
        }
    }
}
