<?php

namespace ChrKo;


class AllianceUpdater extends Updater
{
    public static function clean($server_id, $last_update)
    {
        $result = DB::getConn()->query("SELECT `id` FROM `alliance` WHERE `last_update` < '${last_update}' AND `server_id` = '${server_id}';");
        $deleted_alliance_ids = array_map(function ($v) {
            return intval($v[0]);
        }, $result->fetch_all());
        $result->close();

        if (count($deleted_alliance_ids) > 0) {
            $query = 'DELETE FROM `alliance_member` WHERE `alliance_id` IN (' . implode(',', $deleted_alliance_ids) . ') AND `server_id` = \'' . $server_id . '\';';
            DB::getConn()->query($query);
            $query = 'DELETE FROM `alliance` WHERE `id` IN (' . implode(',', $deleted_alliance_ids) . ') AND `server_id` = \'' . $server_id . '\';';
            DB::getConn()->query($query);
        }
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
