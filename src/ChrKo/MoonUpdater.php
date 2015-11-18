<?php

namespace ChrKo;


class MoonUpdater extends Updater
{
    protected $size = 2000;

    public static function clean($server_id, $last_update)
    {
        // TODO: Implement clean() method.
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
        return 'ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);';
    }
}