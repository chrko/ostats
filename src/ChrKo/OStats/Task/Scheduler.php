<?php

namespace ChrKo\OStats\Task;


use ChrKo\OStats\DB;

class Scheduler {
    /**
     * @var bool
     */
    public static $forceReschedule = false;

    /**
     * @param \ChrKo\OStats\Task\TaskInterface $task
     * @return array
     */
    public static function prepare(TaskInterface $task) {
        $data = [
            'due_time_int' => $task->getDueTime(),
            'job_type'     => $task->getJobType(),
            'slug'         => $task->getSlug(),
            'job'          => serialize($task),
        ];

        array_walk($data, function (&$v, $k) {
            switch ($k) {
                case 'job_type':
                case 'job':
                case 'slug':
                    $v = '\'' . DB::getConn()->real_escape_string($v) . '\'';
                    break;
                case 'due_time_int':
                    $v = (int) $v;
                    break;
            }
        });

        return $data;
    }

    /**
     * @param \ChrKo\OStats\Task\TaskInterface $task
     * @param bool $forceReschedule
     * @throws \Exception
     */
    public static function queue(TaskInterface $task, $forceReschedule = false) {
        $data = self::prepare($task);

        $query = 'SELECT `due_time_int` FROM `tasks`'
            . ' WHERE `job_type` = :job_type: AND `slug` = :slug:';

        $result = DB::getConn()->query(DB::namedReplace($query, $data));

        if ($result->num_rows == 0) {
            $result->free();
            $query =
                'INSERT INTO `tasks` (`due_time_int`, `job_type`, `slug`, `job`)'
                . ' VALUES (:due_time_int:, :job_type:, :slug:, :job:)';

            $query = DB::namedReplace($query, $data);
            DB::getConn()->query($query);
            return;
        }

        $result_data = $result->fetch_assoc();

        if ($forceReschedule || self::$forceReschedule || (int) $result_data['due_time_int'] < $task->getDueTime()) {
            $query =
                'REPLACE INTO `tasks` (`due_time_int`, `job_type`, `slug`, `job`)'
                . ' VALUES (:due_time_int:, :job_type:, :slug:, :job:)';

            $query = DB::namedReplace($query, $data);
            DB::getConn()->query($query);
        }
    }
}
