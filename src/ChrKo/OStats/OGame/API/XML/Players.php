<?php

namespace ChrKo\OStats\OGame\API\XML;


use ChrKo\OStats\OGame\API\XML;
use ChrKo\XmlReaderProxy\Exceptions\UnknownElementException;
use ChrKo\XmlReaderProxy\XmlReader;

class Players {
    const UPDATE_INTERVAL = 24 * 60 * 60;

    public static function readData($serverId) {
        $xmlReader = new XmlReader();
        $xmlReader->open(XML::getServerBaseById($serverId) . 'api/players.xml');

        $xmlReader->read();
        if ($xmlReader->name != 'players') {
            throw new UnknownElementException('players', $xmlReader->name);
        }

        $lastUpdateInt = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $lastUpdateInt);

        $players = [];

        while ($xmlReader->read(false)) {
            if ($xmlReader->nodeType !== XmlReader::ELEMENT) {
                continue;
            }

            $player = [];

            $player['id'] = $xmlReader->getAttribute('id');
            $player['name'] = $xmlReader->getAttribute('name');

            $statusString = $xmlReader->getAttribute('status', '');

            $player['admin'] = strpos($statusString, 'a') !== false;
            $player['vacation'] = strpos($statusString, 'v') !== false;
            $player['inactive_long'] = strpos($statusString, 'I') !== false;
            $player['inactive'] = $player['inactive_long'] || strpos($statusString, 'i') !== false;
            $player['outlaw'] = strpos($statusString, 'o') !== false;
            $player['banned'] = strpos($statusString, 'b') !== false;

            $player['alliance_id'] = $xmlReader->getAttribute('alliance', false);

            $players[$player['id']] = $player;
        }

        return [
            'server_id'       => $serverId,
            'last_update'     => $lastUpdate,
            'last_update_int' => $lastUpdateInt,
            'players'         => $players,
        ];
    }
}
