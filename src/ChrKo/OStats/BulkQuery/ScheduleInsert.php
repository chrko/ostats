<?php

namespace ChrKo\OStats\BulkQuery;


class ScheduleInsert extends AbstractExecutor
{
    public function clean($server_id, $last_update)
    {
    }

    protected function getQueryStart()
    {
        return 'INSERT IGNORE INTO `tasks` (`due_time`, `server_id`, `endpoint`, `category`, `type`, `job`) VALUES' . "\n";
    }

    protected function getQueryPart()
    {
        return ' (:due_time, :server_id, :endpoint, :category, :type, :job),' . "\n";
    }

    protected function getQueryEnd()
    {
        return '';
    }
}
