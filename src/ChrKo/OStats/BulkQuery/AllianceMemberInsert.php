<?php

namespace ChrKo\OStats\BulkQuery;

class AllianceMemberInsert extends AbstractExecutor
{
    public function clean($server_id, $last_update_int)
    {
        $this->dbConn->query("DELETE FROM `alliance_member` WHERE `last_update_int` < ${last_update_int} AND `server_id` = '${server_id}';");
    }

    protected function getQueryStart()
    {
        return 'INSERT INTO `alliance_member` (`server_id`, `alliance_id`, `player_id`, `first_seen_int`, `last_update_int`) VALUES ' . "\n";
    }

    protected function getQueryPart()
    {
        return '(:server_id:, :alliance_id:, :player_id:, :last_update_int:, :last_update_int:),' . "\n";
    }

    protected function getQueryEnd()
    {
        return "\n" . ' ON DUPLICATE KEY UPDATE `last_update_int` = VALUES(`last_update_int`) ';
    }
}
