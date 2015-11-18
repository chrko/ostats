<?php

$start = time();

require_once('bootstrap.php');

use ChrKo\XMLReaderProxy;
use ChrKo\Scheduler;

if ($argc == 2) {
    $server_ids = [
        $argv[1]
    ];

} else {
    $server_ids = explode("\n", file_get_contents('initial.list'));
}

$serverBases = array();

foreach ($server_ids as $server_id) {
    if (strlen($server_id) < 3) continue;
    showMemUsage();
    $xml = new XMLReaderProxy();
    $xml->open(getServerBaseById($server_id) . '/api/universes.xml');

    $xml->read(true);

    if (!$xml->name == 'universes') {
        throw new Exception;
    }

    while ($xml->read()) {
        if ($xml->nodeType !== XMLReaderProxy::ELEMENT) {
            continue;
        }

        $serverBases[] = $xml->getAttribute('href');
    }
}

$serverBases = array_unique($serverBases);

//$serverBases = ['http://s127-de.ogame.gameforge.com',];

$counter = 0;

foreach ($serverBases as $serverBase) {
    echo date('H:i:s ', time() - $start);
    showMemUsage();
    $allianceData = readAllianceData($serverBase);

    foreach (Scheduler::getAllowedEndpoints() as $endpoint) {
        if ($endpoint != 'highscore') {
            Scheduler::schedule(
                \ChrKo\DB::formatTimestamp(),
                $allianceData['server_id'],
                $endpoint
            );
            $counter++;
        } else {
            foreach (['1', '2'] as $category) {
                foreach (range(0, 7) as $type) {
                    Scheduler::schedule(
                        \ChrKo\DB::formatTimestamp(),
                        $allianceData['server_id'],
                        $endpoint,
                        $category,
                        $type
                    );
                    $counter++;
                }
            }
        }
    }
}

echo "\nElapsed time: ", date('H:i:s ', time() - $start);
