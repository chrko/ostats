<?php

require_once('bootstrap.php');

use ChrKo\AllianceMemberUpdater;
use ChrKo\XMLReaderProxy;

if ($argc == 2) {
    $startServerBase = [
        getServerBaseById($argv[1])
    ];

} else {
    $startServerBase = [
        'http://s1-de.ogame.gameforge.com',
        'http://s1-en.ogame.gameforge.com',
        'http://s101-ar.ogame.gameforge.com',
        'http://s1-br.ogame.gameforge.com',
        'http://s670-en.ogame.gameforge.com',
    ];

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
