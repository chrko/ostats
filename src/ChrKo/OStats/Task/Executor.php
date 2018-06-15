<?php

namespace ChrKo\OStats\Task;

use ChrKo\OStats\DB;
use ChrKo\OStats\XmlApiDataProcessor;

class Executor {
    /**
     * @var bool
     */
    protected static $stop = false;
    /**
     * @var string
     */
    protected $whereRestriction;
    /**
     * @var string
     */
    protected $minimalWhereRestriction;
    /**
     * @var bool
     */
    protected $allowRestrictionIgnorance = true;

    public function __construct($whereRestriction = '', $allowRestrictionIgnorance = true) {
        if (!is_string($whereRestriction)) {
            throw new \InvalidArgumentException;
        }
        $this->minimalWhereRestriction = DISABLE_PLAYER ? ' AND `job_type` != \'xml-player\' ' : '';

        $this->whereRestriction = $whereRestriction . $this->minimalWhereRestriction;
        $this->allowRestrictionIgnorance = (bool) $allowRestrictionIgnorance;
    }

    public static function stop() {
        self::$stop = true;
    }

    public function work() {
        $where = $this->whereRestriction;
        $emptied = false;
        while (true) {
            if ($this->whereRestriction == $where || $emptied) {
                echo DB::formatTimestamp(), ' ';
            }
            if ($this->whereRestriction != '' && $where == $this->minimalWhereRestriction) {
                $emptied = true;
            }
            pcntl_signal_dispatch();
            if (self::$stop) {
                echo 'Received signal to end.', "\n";
                break;
            }

            $db = DB::getConn();

            $sql = 'SELECT `id`, `due_time_int`, `job_type`, `slug`, `job` FROM `tasks` WHERE `running` = 0 AND `due_time_int` <= ' . time() . ' ' . $where
                . ' ORDER BY `due_time_int` ASC LIMIT 1;';
            $result = $db->query($sql);

            if ($result->num_rows == 1) {
                $where = $this->whereRestriction;
                $emptied = false;

                $task = $result->fetch_assoc();
                $result->close();

                $db->query('UPDATE `tasks` SET `running` = 1 WHERE `id` = ' . $task['id']);
                if ($db->affected_rows != 1) {
                    echo '?!? ', $db->affected_rows, ' rows affected', "\n";
                    continue;
                };

                $task['due_time'] = DB::formatTimestamp($task['due_time_int']);
                $task['delay'] = date('H:i:s', time() - (int) $task['due_time_int']);

                echo DB::namedReplace(
                    'task :job_type: on server :slug: due :due_time:, :delay:...',
                    $task
                );

                try {
                    $job = unserialize($task['job']);
                    if ($job instanceof TaskInterface) {
                        $job->run();
                    } else {
                        echo ' ERROR INVALID TASK';
                    }

                    $db->query('DELETE FROM `tasks` WHERE `id` = ' . $task['id']);
                    echo ' finished', "\n";
                } catch (\Exception $e) {
                    echo ' Exception occured:', "\n";
                    echo 'Type: ', get_class($e), ' ';
                    echo 'Message: ', $e->getMessage(), "\n";
                    echo 'Line: ', $e->getLine(), ' ';
                    echo 'File: ', $e->getFile(), "\n";

                    $db->query(
                        'UPDATE `tasks` SET `running` = 0, `due_time_int` = \''
                        . ((int) $task['due_time_int'] + 60 * 10)
                        . '\' WHERE `id` = ' . $task['id']
                    );
                }
            } else {
                $result->close();
                $result = $db->query('SELECT `due_time_int` FROM `tasks` WHERE `running` = 0 ' . $this->whereRestriction . ' ORDER BY `due_time_int` ASC LIMIT 1');

                $timeToSleepMin = !defined('MIN_SLEEP_TIME') ? 30 : MIN_SLEEP_TIME;
                $timeToSleepMax = !defined('MAX_SLEEP_TIME') ? 600 : MAX_SLEEP_TIME;

                $timeToSleep = $timeToSleepMin;
                if ($result->num_rows > 0) {
                    $timeToSleep = (int) $result->fetch_object()->due_time_int - time() + 1;
                    $timeToSleep = $timeToSleep >= $timeToSleepMin ? $timeToSleep : $timeToSleepMin;
                }

                $timeToSleep = $timeToSleep > $timeToSleepMax ? $timeToSleepMax : $timeToSleep;
                $timeToSleep = $timeToSleep < $timeToSleepMin ? $timeToSleepMin : $timeToSleep;

                $result->close();
                if ($timeToSleep == $timeToSleepMax && $where != $this->minimalWhereRestriction && $this->allowRestrictionIgnorance) {
                    $where = $this->minimalWhereRestriction;
                    continue;
                }

                echo "Nothing to do for ${timeToSleep} seconds...\n";
                sleep($timeToSleep);
            }
        }
    }
}
