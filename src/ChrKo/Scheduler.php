<?php

namespace ChrKo;


class Scheduler
{
    protected static $end = false;

    public static function getAllowedEndpoints()
    {
        return [
            'players',
            'alliances',
            'universe',
            'highscore'
        ];
    }

    public static function schedule($due_time, $server_id, $endpoint, $category = 0, $type = 0)
    {
        $data = [
            'due_time' => $due_time,
            'server_id' => $server_id,
            'endpoint' => $endpoint,
            'category' => $category,
            'type' => $type
        ];

        array_walk($data, function (&$v, $k) {
            switch ($k) {
                case 'due_time':
                case 'server_id':
                case 'endpoint':
                    $v = '\'' . DB::getConn()->real_escape_string($v) . '\'';
                    break;
                case 'category':
                case 'type':
                    $v = (int)$v;
                    break;
            }
        });

        $query = 'REPLACE INTO `tasks` (`due_time`, `server_id`, `endpoint`, `category`, `type`)
                            VALUES (:due_time, :server_id, :endpoint, :category, :type)';

        $query = DB::namedReplace($query, $data);
        DB::getConn()->query($query);
    }

    public static function work($types = null)
    {
        $whereType = '';
        if (is_array($types) && count($types) > 0) {
            $whereType .= ' AND (';
            $tmp = '';
            foreach ($types as $type) {
                $type = DB::getConn()->real_escape_string($type);
                $tmp .= " OR `endpoint` = '${type}'";
            }
            $whereType .= substr($tmp, 4);
            $whereType .= ') ';
            unset($tmp);
        }
        $whereTypeSave = $whereType;
        unset($types);

        declare(ticks = 1);

        $signals = signals();

        foreach ($signals as $signal => $signalName) {
            pcntl_signal($signal, 'sig_handler');
        }

        $memory_limit = ini_get('memory_limit');
        $memory_limit = trim($memory_limit);
        $last = strtolower($memory_limit[strlen($memory_limit) - 1]);
        switch ($last) {
            case 'g':
                $memory_limit *= 1024;
            case 'm':
                $memory_limit *= 1024;
            case 'k':
                $memory_limit *= 1024;
        }

        $concurrentIdleTime = 0;

        while (($mem_usage = memory_get_usage()) < $memory_limit - 20 * 1024 ** 2) {
            if ($whereTypeSave == $whereType) {
                echo DB::formatTimestamp(), ' ';
            }
            if (self::$end) {
                echo 'Memory Usage: ', number_format($mem_usage / 1024, 2), "KiB\n";
                echo 'Received signal to end.', "\n";
                break;
            }

            $db = DB::getConn();

            if (!$db->autocommit(false)) {
                echo 'Cannot disable autocommit';
                sleep(60);
                continue;
            }

            $db->query('LOCK TABLES `tasks` WRITE;');

            $result = $db->query(
                'SELECT * FROM `tasks` WHERE `running` = 0 AND `due_time` <= \'' . DB::formatTimestamp() . '\' ' . $whereType . 'ORDER BY `due_time` ASC LIMIT 1;'
            );

            if ($result->num_rows == 1) {
                $whereType = $whereTypeSave;

                $concurrentIdleTime = 0;
                $task = $result->fetch_assoc();
                $result->close();
                $db->query('UPDATE `tasks` SET `running` = 1 WHERE `id` = ' . $task['id']);
                if ($db->affected_rows != 1) {
                    echo '?!? ', $db->affected_rows, ' rows affected', "\n";
                    echo $db->error, "\n";
                    continue;
                };
                $db->query('UNLOCK TABLES;');
                if (!$db->autocommit(true)) {
                    echo 'Unable to enable autocommit';
                    continue;
                }

                $serverBase = getServerBaseById($task['server_id']);

                $task['delay'] = date('H:i:s', time() - strtotime($task['due_time']));

                echo DB::namedReplace(
                    'task :endpoint on server :server_id (:category, :type) due :due_time, :delay...',
                    $task
                );
                try {
                    switch ($task['endpoint']) {
                        case 'players':
                            $playerData = readPlayerData($serverBase);
                            bulkUpdatePlayerData($playerData);
                            PlayerUpdater::clean($playerData['server_id'], $playerData['last_update']);
                            bulkUpdateAllianceMemberByPlayerData($playerData);
                            AllianceMemberUpdater::clean($playerData['server_id'], $playerData['last_update']);
                            break;
                        case 'alliances':
                            $allianceData = readAllianceData($serverBase);
                            bulkUpdateAllianceData($allianceData);
                            AllianceUpdater::clean($allianceData['server_id'], $allianceData['last_update']);
                            bulkUpdateAllianceMemberByAllianceData($allianceData);
                            AllianceMemberUpdater::clean($allianceData['server_id'], $allianceData['last_update']);
                            break;
                        case 'universe':
                            bulkUpdateUniverse($serverBase);
                            break;
                        case 'highscore':
                            bulkUpdateHighscore(
                                $serverBase,
                                [$task['category']],
                                [$task['type']]
                            );
                            break;
                        default:
                            throw new \Exception('unknown endpoint ' . $task['endpoint']);
                    }
                    $db->query('DELETE FROM `tasks` WHERE `id` = ' . $task['id']);
                    echo ' finished', "\n";
                } catch (\Exception $e) {
                    echo ' Exception occured:', "\n";
                    echo 'Line: ', $e->getLine(), ' ';
                    echo 'Message: ', $e->getMessage(), "\n";

                    $db->query(
                        'UPDATE `tasks` SET `running` = 0, `due_time` = \''
                        . DB::formatTimestamp(strtotime($task['due_time']) + 60 * 10)
                        . '\' WHERE `id` = ' . $task['id']
                    );
                }
            } else {
                $result->close();
                $db->query('UNLOCK TABLES;');
                $db->autocommit(true);
                $result = $db->query('SELECT * FROM `tasks` WHERE `running` = 0 ' . $whereTypeSave . ' ORDER BY `due_time` ASC LIMIT 1');

                $timeToSleepMin = !defined('MIN_SLEEP_TIME') ? 30 : MIN_SLEEP_TIME;
                $timeToSleepMax = !defined('MAX_SLEEP_TIME') ? 600 : MAX_SLEEP_TIME;

                $timeToSleep = $timeToSleepMin;
                if ($result->num_rows == 1) {
                    $timeToSleep = strtotime($result->fetch_object()->due_time) - time() + 1;
                    $timeToSleep = $timeToSleep >= $timeToSleepMin ? $timeToSleep : $timeToSleepMin;
                }

                $timeToSleep = $timeToSleep > $timeToSleepMax ? $timeToSleepMax : $timeToSleep;
                $timeToSleep = $timeToSleep < $timeToSleepMin ? $timeToSleepMin : $timeToSleep;

                $result->close();
                if ($timeToSleep == $timeToSleepMax && $whereType != '') {
                    $whereType = '';
                    continue;
                }

                echo "Nothing to do for ${timeToSleep} seconds...\n";
                $concurrentIdleTime += $timeToSleep;
                sleep($timeToSleep);
            }
        }
    }

    public static function end()
    {
        self::$end = true;
    }
}
