<?php

require_once('bootstrap.php');

$result = \ChrKo\DB::getConn()->query('SELECT COUNT(*) FROM `tasks` WHERE `due_time` < \'' . \ChrKo\DB::formatTimestamp() . '\'');

$delayedTasks = 0;

if ($result->num_rows == 1) {
    $delayedTasks = $result->fetch_array()[0];
}
$delayedTasks = $delayedTasks < 0 ? 0 : $delayedTasks;

echo 'Delay tasks ', $delayedTasks, "\n";
