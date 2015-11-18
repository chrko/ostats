<?php

require_once('bootstrap.php');

$result = \ChrKo\DB::getConn()->query('SELECT MAX(`due_time`) FROM `tasks` WHERE `due_time` < \'' . \ChrKo\DB::formatTimestamp() . '\'');

$delay = 0;

if ($result->num_rows == 1) {
    $delay = strtotime($result->fetch_array()[0]) - time();
}
$delay = $delay < 0 ? 0 : $delay;

echo 'Delay: ' .date('H:i:s', $delay), "\n";
