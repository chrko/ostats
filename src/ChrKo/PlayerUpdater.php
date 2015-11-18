<?php

namespace ChrKo;


class PlayerUpdater extends Updater
{
    public static function clean($server_id, $last_update)
    {
        $result = DB::getConn()->query("SELECT `id` FROM `player` WHERE `last_update` < '${last_update}' AND `server_id` = '${server_id}';");
        $deleted_player_ids = array_map(function ($v) {
            return intval($v[0]);
        }, $result->fetch_all());

        $result->close();
        if (count($deleted_player_ids) > 0) {
            $query = 'DELETE FROM `alliance_member` WHERE `player_id` IN (' . implode(', ', $deleted_player_ids) . ') AND `server_id` = \'' . $server_id . '\';';
            DB::getConn()->query($query);
            $query = 'DELETE FROM `player` WHERE `id` IN (' . implode(', ', $deleted_player_ids) . ') AND `server_id` = \'' . $server_id . '\';';
            DB::getConn()->query($query);
        }
    }

    protected function getQueryStart()
    {
        return 'INSERT INTO `player` (`server_id`, `id`, `name`, `vacation`, `inactive`, `inactive_long`, `banned`, `outlaw`, `admin`, `last_update`) VALUES ' . "\n";
    }

    protected function getQueryPart()
    {
        return '(:server_id, :id, :name, :vacation, :inactive, :inactive_long, :banned, :outlaw, :admin, :last_update),' . "\n";
    }

    protected function getQueryEnd()
    {
        return 'ON DUPLICATE KEY UPDATE
                    `name`          = VALUES(`name`),
                    `vacation`      = VALUES(`vacation`),
                    `inactive`      = VALUES(`inactive`),
                    `inactive_long` = VALUES(`inactive_long`),
                    `banned`        = VALUES(`banned`),
                    `outlaw`        = VALUES(`outlaw`),
                    `admin`         = VALUES(`admin`),
                    `last_update`   = VALUES(`last_update`)';
    }
}
