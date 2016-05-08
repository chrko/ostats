<?php

namespace ChrKo\OStats\BulkQuery;


class MoonInsert extends AbstractExecutor
{
    public function clean($server_id, $last_update_int)
    {
        $this->dbConn->query("DELETE FROM `moon` WHERE `last_update_int` < ${last_update_int} AND `server_id` = '${server_id}';");
    }

    protected function getQueryStart()
    {
        return 'INSERT INTO `moon` (
                `server_id`,
                `id`,
                `planet_id`,
                `size`,
                `name`,
                `first_seen_int`,
                `last_update_int`
            ) VALUES ';
    }

    protected function getQueryPart()
    {
        return '(:server_id, :id, :planet_id, :size, :name, :last_update_int, :last_update_int),' . "\n";
    }

    protected function getQueryEnd()
    {
        return 'ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `last_update_int` = VALUES(`last_update_int`);';
    }
}
