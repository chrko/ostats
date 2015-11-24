<?php

namespace ChrKo\OStats\BulkQuery;


class MoonInsert extends AbstractExecutor
{
    public function clean($server_id, $last_update)
    {
        $this->dbConn->query("DELETE FROM `moon` WHERE `last_update` < '${last_update}' AND `server_id` = '${server_id}';");
    }

    protected function getQueryStart()
    {
        return 'INSERT INTO `moon` (
                `server_id`,
                `id`,
                `planet_id`,
                `size`,
                `name`,
                `first_seen`,
                `last_update`
            ) VALUES ';
    }

    protected function getQueryPart()
    {
        return '(:server_id, :id, :planet_id, :size, :name, :last_update, :last_update),' . "\n";
    }

    protected function getQueryEnd()
    {
        return 'ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `last_update` = VALUES(`last_update`);';
    }
}
