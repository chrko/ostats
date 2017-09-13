<?php

namespace ChrKo\OStats\OGame\API\XML;


use ChrKo\OStats\OGame\API\XML;
use ChrKo\XmlReaderProxy\Exceptions\UnknownElementException;
use ChrKo\XmlReaderProxy\XmlReader;

class Player {
    const UPDATE_INTERVAL = 7 * 24 * 60 * 60;

    public static function readData($serverId, $playerId) {
        $xmlReader = new XmlReader();
        $xmlReader->open(XML::getServerBaseById($serverId) . 'api/playerData.xml?id=' . $playerId);

        $xmlReader->read();
        if ($xmlReader->name != 'playerData') {
            throw new UnknownElementException('playerData', $xmlReader->name);
        }

        $lastUpdateInt = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $lastUpdateInt);

        $data = [
            'server_id'       => $serverId,
            'player_id'       => $playerId,
            'last_update'     => $lastUpdate,
            'last_update_int' => $lastUpdateInt,
            'highscore'       => [],
            'planets'         => [],
            'moons'           => [],
            'alliance_id'     => 0,
            'alliance_name'   => '',
            'alliance_tag'    => '',
        ];

        if ($xmlReader->getAttribute('id') != $playerId) {
            throw new \Exception('Something very wrong');
        }

        $data['player_name'] = $xmlReader->getAttribute('name');

        $lastPlanetId = 0;

        while ($xmlReader->read(false)) {
            if ($xmlReader->nodeType != XmlReader::ELEMENT) {
                continue;
            }
            switch ($xmlReader->name) {
                case 'position':
                    $data['highscore'][] = [
                        'id'       => $playerId,
                        'type'     => (int) $xmlReader->getAttribute('type'),
                        'position' => (int) $xmlReader->readString(),
                        'points'   => (int) $xmlReader->getAttribute('score'),
                        'ships'    => (int) $xmlReader->getAttribute('ships', 0),
                    ];
                    break;
                case 'planet':
                    $coords = explode(':', $xmlReader->getAttribute('coords'));
                    $data['planets'][] = [
                        'server_id'       => $serverId,
                        'last_update_int' => $lastUpdateInt,
                        'id'              => $xmlReader->getAttribute('id'),
                        'name'            => $xmlReader->getAttribute('name'),
                        'galaxy'          => $coords[0],
                        'system'          => $coords[1],
                        'position'        => $coords[2],
                        'player_id'       => $playerId,
                    ];

                    $lastPlanetId = $xmlReader->getAttribute('id');
                    break;
                case 'moon':
                    if ($lastPlanetId === 0) {
                        throw new \Exception;
                    }
                    $data['moons'][] = [
                        'server_id'       => $serverId,
                        'last_update_int' => $lastUpdateInt,
                        'id'              => $xmlReader->getAttribute('id'),
                        'planet_id'       => $lastPlanetId,
                        'size'            => $xmlReader->getAttribute('size'),
                        'name'            => $xmlReader->getAttribute('name'),
                    ];
                    $lastPlanetId = 0;
                    break;
                case 'alliance':
                    $data['alliance_id'] = (int) $xmlReader->getAttribute('id');
                    break;
                case 'name':
                    if ($data['alliance_id'] === 0) {
                        throw new \Exception;
                    }
                    $data['alliance_name'] = $xmlReader->readString();
                    break;
                case 'tag':
                    if ($data['alliance_id'] === 0) {
                        throw new \Exception;
                    }
                    $data['alliance_tag'] = $xmlReader->readString();
                    break;
                case 'positions':
                case 'planets':
                    break;
                default:
                    throw new UnknownElementException('position|planet|moon|alliance|name|tag|positions|planets', $xmlReader->name);
            }
        }

        return $data;
    }
}