<?php

require_once('bootstrap.php');

$types = array();

if ($argc >= 2) {
    array_shift($argv);
    foreach ($argv as $arg) {
        switch ($arg) {
            case 'highscore':
            case 'players':
            case 'alliances':
            case 'universe':
                $types[] = $arg;
        }
    }
}

\ChrKo\Scheduler::work($types);
