<?php

namespace ChrKo\OStats\BulkQuery;


class AllianceInsert extends AbstractExecutor
{
    public function clean($server_id, $last_update)
    {
        $this->dbConn->query("DELETE FROM `alliance` WHERE `last_update` < '${last_update}' AND `server_id` = '${server_id}';");
    }

    protected function getQueryStart()
    {
        return 'INSERT INTO `alliance` (`server_id`, `id`, `name`, `tag`, `homepage`, `logo`, `open`, `last_update`) VALUES ';
    }

    protected function getQueryPart()
    {
        return "(:server_id, :id, :name, :tag, :homepage, :logo, :open, :last_update),\n";
    }

    protected function getQueryEnd()
    {
        return 'ON DUPLICATE KEY UPDATE
            `name`        = VALUES(`name`),
            `tag`         = VALUES(`tag`),
            `homepage`    = VALUES(`homepage`),
            `logo`        = VALUES(`logo`),
            `open`        = VALUES(`open`),
            `last_update` = VALUES(`last_update`)';
    }
}
