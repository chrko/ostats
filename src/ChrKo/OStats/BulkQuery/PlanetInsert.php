<?php

namespace ChrKo\OStats\BulkQuery;


class PlanetInsert extends AbstractExecutor
{
    public function clean($server_id, $last_update)
    {
        $this->dbConn->query("DELETE FROM `planet` WHERE `last_update` < '${last_update}' AND `server_id` = '${server_id}';");
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
                `first_seen`,
                `last_update`
            ) VALUES ';
    }

    protected function getQueryPart()
    {
        return '(:server_id, :id, :name, :galaxy, :system, :position, :player_id, :last_update, :last_update),' . "\n";
    }

    protected function getQueryEnd()
    {
        return "\n" . 'ON DUPLICATE KEY UPDATE
              `name`        = VALUES(`name`),
              `galaxy`      = VALUES(`galaxy`),
              `system`      = VALUES(`system`),
              `position`    = VALUES(`position`),
              `last_update` = VALUES(`last_update`)
        ;';
    }
}
