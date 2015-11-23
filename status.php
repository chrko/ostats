<?php

require_once('bootstrap.php');

$resultTotalTasks = \ChrKo\DB::getConn()->query('SELECT COUNT(*) AS `count`, `endpoint` FROM `tasks` GROUP BY `endpoint`');
$resultDelayedTasks = \ChrKo\DB::getConn()->query('SELECT COUNT(*) AS `count`, `endpoint` FROM `tasks` WHERE `running` = 0 AND `due_time` < \'' . \ChrKo\DB::formatTimestamp() . '\' GROUP BY `endpoint`');
$resultDelayedTime = \ChrKo\DB::getConn()->query('SELECT MIN(`due_time`) AS `count`, `endpoint` FROM `tasks` WHERE `running` = 0 AND `due_time` < \'' . \ChrKo\DB::formatTimestamp() . '\' GROUP BY `endpoint`');

$buffer = [];
$totalTasksEndpoint = [];

function output($result, $buffer, $stringFormat)
{
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        extract($row);
        $total += (int)$count;
        if (!array_key_exists($endpoint, $buffer)) {
            $buffer[$endpoint] = '';
        }
        $buffer[$endpoint] .= $stringFormat($count, $endpoint);
    }
    return [$total, $buffer, 'total' => $total, 'buffer' => $buffer];
}

list($totalTasks, $buffer) = output(
    $resultTotalTasks,
    $buffer,
    function ($count, $endpoint) use (&$totalTasksEndpoint) {
        $totalTasksEndpoint[$endpoint] = (int)$count;
        return 'Total tasks for endpoint "' . $endpoint . '": ' . $count . "\n";
    }
);

list($totalDelayedTasks, $buffer) = output(
    $resultDelayedTasks,
    $buffer,
    function ($count, $endpoint) use ($totalTasksEndpoint) {
        return 'Delayed tasks for endpoint "' . $endpoint . '": ' . $count . ' | ' . number_format(((int)$count) / $totalTasksEndpoint[$endpoint] * 100, 2) . "%\n";
    }
);

$totalDelayedTime = -1;
list($tmp, $buffer) = output(
    $resultDelayedTime,
    $buffer,
    function ($count, $endpoint) use ($totalTasksEndpoint, &$totalDelayedTime) {
        $timestamp = strtotime($count);
        if ($totalDelayedTime < 0) {
            $totalDelayedTime = $timestamp;
        } else {
            $totalDelayedTime = $totalDelayedTime < $timestamp ? $totalDelayedTime : $timestamp;
        }
        return 'Delay time for endpoint "' . $endpoint . '": ' . date('H:i:s', time() - $timestamp) . "\n";
    }
);

echo 'Total tasks: ' . $totalTasks . "\n";
echo 'Delayed tasks: ' . $totalDelayedTasks . ' | ' . number_format(((int)$totalDelayedTasks) / $totalTasks * 100, 2) . "%\n";
echo 'Delay time: ' . date('H:i:s', time() - $totalDelayedTime) . "\n";
echo "\n";

if($argc > 1) {
    foreach ($buffer as $line) {
        echo $line, "\n";
    }
}
