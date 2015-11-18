<?php

require_once('bootstrap.php');

$resultDelayedTasks = \ChrKo\DB::getConn()->query('SELECT COUNT(*) FROM `tasks` WHERE `running` = 0 AND `due_time` < \'' . \ChrKo\DB::formatTimestamp() . '\'');
$resultDelayedTime = \ChrKo\DB::getConn()->query('SELECT MIN(`due_time`) FROM `tasks` WHERE `running` = 0 AND `due_time` < \'' . \ChrKo\DB::formatTimestamp() . '\'');

$delayedTasks = 0;
$delayedTime = 0;

if ($resultDelayedTasks->num_rows == 1) {
    $delayedTasks = $resultDelayedTasks->fetch_array()[0];
}
$delayedTasks = $delayedTasks < 0 ? 0 : $delayedTasks;

echo 'Delay tasks ', $delayedTasks, "\n";

if ($resultDelayedTime->num_rows == 1) {
    $result = $resultDelayedTime->fetch_array();
    $delayedTime = $result[0] != 'NULL' ? (time() - strtotime($result[0])) : 0;
}

$delayedTime = $delayedTime < 0 ? 0 : $delayedTime;

echo 'Delay time ', date('H:i:s', $delayedTime), "\n";
