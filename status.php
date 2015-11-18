<?php

require_once('bootstrap.php');

$resultTotalTasks = \ChrKo\DB::getConn()->query('SELECT COUNT(*) FROM `tasks`');
$resultDelayedTasks = \ChrKo\DB::getConn()->query('SELECT COUNT(*) FROM `tasks` WHERE `running` = 0 AND `due_time` < \'' . \ChrKo\DB::formatTimestamp() . '\'');
$resultDelayedTime = \ChrKo\DB::getConn()->query('SELECT MIN(`due_time`) FROM `tasks` WHERE `running` = 0 AND `due_time` < \'' . \ChrKo\DB::formatTimestamp() . '\'');

$totalTasks = 1;
$delayedTasks = 0;
$delayedTime = 0;

if ($resultTotalTasks->num_rows == 1) {
    $totalTasks = $resultTotalTasks->fetch_array()[0];
}

$totalTasks = $totalTasks < 1 ? 1 : $totalTasks;

echo 'Total tasks ', $totalTasks, "\n";

if ($resultDelayedTasks->num_rows == 1) {
    $delayedTasks = (int)$resultDelayedTasks->fetch_array()[0];
}

$delayedTasks = $delayedTasks < 0 ? 0 : $delayedTasks;

echo 'Delayed tasks ', $delayedTasks, ' | ', number_format($delayedTasks / $totalTasks * 100, 2), "%\n";

if ($resultDelayedTime->num_rows == 1) {
    $result = $resultDelayedTime->fetch_array();
    $delayedTime = $result[0] != null ? (time() - strtotime($result[0])) : 0;
}

echo 'Delay time ', $delayedTime <= 0 ? '00:00:00' : date('H:i:s', $delayedTime), "\n";
