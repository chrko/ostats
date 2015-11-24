<?php

namespace ChrKo\OStats\BulkQuery;


use ChrKo\OStats\DB;

class HighscoreInsert extends AbstractExecutor
{
    protected $batchSize = 2000;
    protected $category;

    public function __construct(DB $dbConn,$category)
    {
        if (strpos($category, 'player') === false && strpos($category, 'alliance')) {
            throw new \InvalidArgumentException;
        }
        $this->category = $category;

        parent::__construct($dbConn);
    }

    public function clean($server_id, $last_update)
    {
    }

    protected function getQueryStart()
    {
        return
            'INSERT IGNORE INTO `highscore_' . $this->category . '`(
                `server_id`,
                `id`,
                `type`,
                `points`,
                `seen`
            ) VALUES ';
    }

    protected function getQueryPart()
    {
        return
            '(:server_id, :id, :type, :points, :seen),' . "\n";
    }

    protected function getQueryEnd()
    {
        return '';
    }
}
