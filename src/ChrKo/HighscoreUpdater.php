<?php

namespace ChrKo;


class HighscoreUpdater extends Updater
{
    protected $size = 2000;
    protected $category;

    public function __construct($category)
    {
        if (strpos($category, 'player') === false && strpos($category, 'alliance')) {
            throw new \Exception('unknown type');
        }
        $this->category = $category;

        parent::__construct();
    }

    public static function clean($server_id, $last_update)
    {
    }

    protected function getQueryStart()
    {
        return
            'INSERT IGNORE INTO `highscore_' . $this->category . '`(
                `server_id`,
                `' . $this->category . '_id`,
                `type`,
                `points`,
                `seen`
            ) VALUES ';
    }

    protected function getQueryPart()
    {
        return
            '(:server_id, :' . $this->category . '_id, :type, :points, :seen),' . "\n";
    }

    protected function getQueryEnd()
    {
        return "\n" . 'ON DUPLICATE KEY UPDATE `seen` = VALUES(`seen`)';
    }
}