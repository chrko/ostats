<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 9/13/17
 * Time: 6:43 PM
 */

namespace ChrKo\OStats\OGame\API\XML;


use ChrKo\OStats\OGame\API\XML;
use ChrKo\XmlReaderProxy\Exceptions\UnknownElementException;
use ChrKo\XmlReaderProxy\XmlReader;

class Universe {
    const UPDATE_INTERVAL = 7 * 24 * 60 * 60;

    public static function readData($serverId) {
        $xmlReader = new XmlReader();
        $xmlReader->open(XML::getServerBaseById($serverId) . 'api/universe.xml');

        $xmlReader->read();
        if ($xmlReader->name != 'universe') {
            throw new UnknownElementException('universe', $xmlReader->name);
        }

        $lastUpdateInt = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $lastUpdateInt);

        $lastPlanetId = 0;

        $planets = [];
        $moons = [];

        while ($xmlReader->read(false)) {
            if ($xmlReader->nodeType !== XmlReader::ELEMENT) {
                continue;
            }

            switch ($xmlReader->name) {
                case 'planet':
                    $coords = explode(':', $xmlReader->getAttribute('coords'));
                    $planets[] = [
                        'server_id' => $serverId,
                        'id'        => $xmlReader->getAttribute('id'),
                        'name'      => $xmlReader->getAttribute('name'),
                        'galaxy'    => $coords[0],
                        'system'    => $coords[1],
                        'position'  => $coords[2],
                        'player_id' => $xmlReader->getAttribute('player'),
                    ];

                    $lastPlanetId = $xmlReader->getAttribute('id');
                    break;
                case 'moon':
                    if ($lastPlanetId === 0) {
                        throw new \Exception;
                    }
                    $moons[] = [
                        'server_id' => $serverId,
                        'id'        => $xmlReader->getAttribute('id'),
                        'planet_id' => $lastPlanetId,
                        'size'      => $xmlReader->getAttribute('size'),
                        'name'      => $xmlReader->getAttribute('name'),
                    ];
                    break;
                default:
                    throw new UnknownElementException('planet|moon', $xmlReader->name);
            }
        }

        return [
            'server_id'       => $serverId,
            'last_update'     => $lastUpdate,
            'last_update_int' => $lastUpdateInt,
            'planets'         => $planets,
            'moons'           => $moons,
        ];
    }
}
