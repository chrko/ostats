<?php

namespace ChrKo\OStats\BulkQuery;


class PlayerInsert extends AbstractExecutor
{
    public function clean($server_id, $last_update_int)
    {
        $this->dbConn->query("DELETE FROM `player` WHERE `last_update_int` < ${last_update_int} AND `server_id` = '${server_id}';");
    }

    protected function getQueryStart()
    {
        return 'INSERT INTO `player` (`server_id`, `id`, `name`, `vacation`, `inactive`, `inactive_long`, `banned`, `outlaw`, `admin`, `last_update_int`) VALUES ' . "\n";
    }

    protected function getQueryPart()
    {
        return '(:server_id:, :id:, :name:, :vacation:, :inactive:, :inactive_long:, :banned:, :outlaw:, :admin:, :last_update_int:),' . "\n";
    }

    protected function getQueryEnd()
    {
        return 'ON DUPLICATE KEY UPDATE
                    `name`              = VALUES(`name`),
                    `vacation`          = VALUES(`vacation`),
                    `inactive`          = VALUES(`inactive`),
                    `inactive_long`     = VALUES(`inactive_long`),
                    `banned`            = VALUES(`banned`),
                    `outlaw`            = VALUES(`outlaw`),
                    `admin`             = VALUES(`admin`),
                    `last_update_int`   = VALUES(`last_update_int`)';
    }
}
