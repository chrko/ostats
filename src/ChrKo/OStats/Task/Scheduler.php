<?php

namespace ChrKo\OStats\Task;


use ChrKo\OStats\DB;

class Scheduler
{
    public static function queue(XmlApiUpdate $task)
    {
        $data = [
            'due_time' => DB::formatTimestamp($task->getDueTime()),
            'server_id' => $task->getServerId(),
            'endpoint' => $task->getEndpoint(),
            'category' => $task->getCategory(),
            'type' => $task->getType(),
            'job' => serialize($task)
        ];

        array_walk($data, function (&$v, $k) {
            switch ($k) {
                case 'due_time':
                case 'server_id':
                case 'endpoint':
                case 'job':
                    $v = '\'' . DB::getConn()->real_escape_string($v) . '\'';
                    break;
                case 'category':
                case 'type':
                    $v = (int)$v;
                    break;
            }
        });

        $query =
            'REPLACE INTO `tasks` (`due_time`, `server_id`, `endpoint`, `category`, `type`, `job`)'
            . ' VALUES (:due_time, :server_id, :endpoint, :category, :type, :job)';

        $query = DB::namedReplace($query, $data);
        DB::getConn()->query($query);
    }
}
