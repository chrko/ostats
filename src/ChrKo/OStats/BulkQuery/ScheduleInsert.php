<?php

namespace ChrKo\OStats\BulkQuery;


class ScheduleInsert extends AbstractExecutor
{
    /**
     * @var bool
     */
    public static $forceReschedule = false;

    /**
     * @param $server_id
     * @param $last_update
     */
    public function clean($server_id, $last_update)
    {
    }

    /**
     * @return string
     */
    protected function getQueryStart()
    {
        $end = 'INTO `tasks` (`due_time_int`, `server_id`, `endpoint`, `category`, `type`, `job`) VALUES' . "\n";
        if (self::$forceReschedule) {
            return 'REPLACE ' . $end;
        }
        return 'INSERT IGNORE ' . $end;
    }

    /**
     * @return string
     */
    protected function getQueryPart()
    {
        return ' (:due_time_int, :server_id, :endpoint, :category, :type, :job),' . "\n";
    }

    /**
     * @return string
     */
    protected function getQueryEnd()
    {
        return '';
    }
}
