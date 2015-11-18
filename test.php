<?php

require_once('bootstrap.php');

use ChrKo\AllianceMemberUpdater;
use ChrKo\XMLReaderProxy;

if ($argc == 2) {
    $startServerBase = [
        getServerBaseById($argv[1])
    ];

} else {
    $server_ids = [
        'ar101',
        'br1',
        'cz101',
        'de1',
        'dk1',
        'en1',
        'es1',
        'fi101',
        'fr1',
        'gr1',
        'hu101',
        'hr104',
        'it1',
        'jp101',
        'mx101',
        'nl101',
        'no101',
        'pl1',
        'pt101',
        'ru1',
        'ro1',
        'se1',
        'si101',
        'sk101',
        'tr1',
        'tw101',
        'us1'
    ];

    $startServerBase = [
        'http://s670-en.ogame.gameforge.com',
    ];

    foreach ($server_ids as $server_id) {
        $startServerBase[] = getServerBaseById($server_id);
    }
}


$serverBases = array();

foreach ($startServerBase as $startBase) {

    $xml = new XMLReaderProxy();
    $xml->open($startBase . '/api/universes.xml');

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

foreach ($serverBases as $serverBase) {
    $allianceData = readAllianceData($serverBase);
    echo $allianceData['server_id'], "\n";
    $playerData = readPlayerData($serverBase);

    bulkUpdateAllianceData($allianceData);
    bulkUpdatePlayerData($playerData);
    bulkUpdateHighscore($serverBase);

    if ($allianceData['timestamp'] >= $playerData['timestamp']) {
        bulkUpdateAllianceMemberByPlayerData($playerData);
        bulkUpdateAllianceMemberByAllianceData($allianceData);
        AllianceMemberUpdater::clean($allianceData['server_id'], $allianceData['last_update']);
    } else {
        bulkUpdateAllianceMemberByAllianceData($allianceData);
        bulkUpdateAllianceMemberByPlayerData($playerData);
        AllianceMemberUpdater::clean($playerData['server_id'], $playerData['last_update']);
    }

    bulkUpdateUniverse($serverBase);
}

var_dump(true);
