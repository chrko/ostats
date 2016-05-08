<?php

namespace ChrKo\OStats\BulkQuery;


class PlanetInsert extends AbstractExecutor
{
    protected $batchSize = 1000;

    public function clean($server_id, $last_update_int)
    {
        $this->dbConn->query("DELETE FROM `planet` WHERE `last_update_int` < ${last_update_int} AND `server_id` = '${server_id}';");
    }

    protected function getQueryStart()
    {
        return 'INSERT INTO `planet` (
                `server_id`,
                `id`,
                `name`,
                `galaxy`,
                `system`,
                `position`,
                `player_id`,
                `first_seen_int`,
                `last_update_int`
            ) VALUES ';
    }

    protected function getQueryPart()
    {
        return '(:server_id, :id, :name, :galaxy, :system, :position, :player_id, :last_update_int, :last_update_int),' . "\n";
    }

    protected function getQueryEnd()
    {
        return "\n" . 'ON DUPLICATE KEY UPDATE
              `name`            = VALUES(`name`),
              `galaxy`          = VALUES(`galaxy`),
              `system`          = VALUES(`system`),
              `position`        = VALUES(`position`),
              `last_update_int` = VALUES(`last_update_int`)
        ;';
    }
}
