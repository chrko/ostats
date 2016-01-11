<?php

namespace ChrKo\OStats\Task;


use ChrKo\OStats\DB;

class Scheduler {
    public static $forceReschedule = false;

    public static function queue(XmlApiUpdate $task) {
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
                    $v = (int) $v;
                    break;
            }
        });

        $query = 'SELECT `due_time` FROM `tasks`'
            . ' WHERE `server_id` = :server_id AND `endpoint` = :endpoint'
            . ' AND `category` = :category AND `type` = :type';

        $result = DB::getConn()->query(DB::namedReplace($query, $data));

        if ($result->num_rows == 0) {
            $result->free();
            $query =
                'INSERT INTO `tasks` (`due_time`, `server_id`, `endpoint`, `category`, `type`, `job`)'
                . ' VALUES (:due_time, :server_id, :endpoint, :category, :type, :job)';

            $query = DB::namedReplace($query, $data);
            DB::getConn()->query($query);
            return;
        }

        $result_data = $result->fetch_assoc();

        if (self::$forceReschedule || strtotime($result_data['due_time']) < $task->getDueTime()) {
            $query =
                'REPLACE INTO `tasks` (`due_time`, `server_id`, `endpoint`, `category`, `type`, `job`)'
                . ' VALUES (:due_time, :server_id, :endpoint, :category, :type, :job)';

            $query = DB::namedReplace($query, $data);
            DB::getConn()->query($query);
        }
    }
}
