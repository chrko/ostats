<?php

namespace ChrKo\OStats\BulkQuery;


use ChrKo\OStats\DB;

class HighscoreInsert extends AbstractExecutor
{
    protected $batchSize = 2000;
    protected $category;
    protected $type;

    public function __construct(DB $dbConn, $category, $type)
    {
        if (!in_array($category, range(1, 2))) {
            throw new \InvalidArgumentException;
        }
        $this->category = $category;
        if (!in_array($type, range(0, 7))) {
            throw new \InvalidArgumentException;
        }
        $this->type = $type;

        {
            parent::__construct($dbConn);
        }
    }

    public function clean($server_id, $last_update)
    {
    }

    protected function getQueryStart()
    {
        if ($this->category == 1 && $this->type == 3) {
            return
                'INSERT IGNORE INTO `highscore_' . $this->category . '_' . $this->type
                . '` (`server_id`, `id`, `points`, `ships`, `seen`) VALUES' . "\n";
        }

        return
            'INSERT IGNORE INTO `highscore_' . $this->category . '_' . $this->type
            . '` (`server_id`, `id`, `points`, `seen`) VALUES' . "\n";
    }

    protected function getQueryPart()
    {
        if ($this->category == 1 && $this->type == 3) {
            return '(:server_id, :id, :points, :ships, :seen),' . "\n";
        }
        return '(:server_id, :id, :points, :seen),' . "\n";
    }

    protected function getQueryEnd()
    {
        return '';
    }
}
