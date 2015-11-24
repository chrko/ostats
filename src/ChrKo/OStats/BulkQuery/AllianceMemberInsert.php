<?php

namespace ChrKo\OStats\BulkQuery;

class AllianceMemberInsert extends AbstractExecutor
{
    public function clean($server_id, $last_update)
    {
        $this->dbConn->query("DELETE FROM `alliance_member` WHERE `last_update` < '${last_update}' AND `server_id` = '${server_id}';");
    }

    protected function getQueryStart()
    {
        return 'INSERT INTO `alliance_member` (`server_id`, `alliance_id`, `player_id`, `first_seen`, `last_update`) VALUES ' . "\n";
    }

    protected function getQueryPart()
    {
        return '(:server_id, :alliance_id, :player_id, :last_update, :last_update),' . "\n";
    }

    protected function getQueryEnd()
    {
        return "\n" . 'ON DUPLICATE KEY UPDATE `last_update` = VALUES(`last_update`)';
    }
}
