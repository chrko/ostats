<?php

namespace ChrKo;


class PlanetUpdater extends Updater
{
    protected $size = 2000;

    public static function clean($server_id, $last_update)
    {
        // TODO: Implement clean() method.
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