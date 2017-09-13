<?php

namespace ChrKo\OStats\OGame\API\XML;


use ChrKo\OStats\OGame\API\XML;
use ChrKo\XmlReaderProxy\Exceptions\UnknownElementException;
use ChrKo\XmlReaderProxy\XmlReader;

class Alliances {

    const UPDATE_INTERVAL = 24 * 60 * 60;

    public static function readData($serverId) {
        $xmlReader = new XmlReader();
        $xmlReader->open(XML::getServerBaseById($serverId) . 'api/alliances.xml');

        $xmlReader->read();
        if ($xmlReader->name != 'alliances') {
            throw new UnknownElementException('alliances', $xmlReader->name);
        }

        $lastUpdateInt = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $lastUpdateInt);

        $alliances = [];
        $alliance = [];

        while ($xmlReader->read(false)) {
            if ($xmlReader->nodeType !== XmlReader::ELEMENT) {
                continue;
            }

            switch ($xmlReader->name) {
                case 'alliance':
                    if (count($alliance) > 0) {
                        $alliances[$alliance['id']] = $alliance;
                        $alliance = [];
                    }
                    $alliance['id'] = $xmlReader->getAttribute('id');
                    $alliance['name'] = $xmlReader->getAttribute('name');
                    $alliance['tag'] = $xmlReader->getAttribute('tag');
                    $alliance['open'] = (bool) $xmlReader->getAttribute('open', false);

                    $alliance['homepage'] = $xmlReader->getAttribute('homepage', false);
                    $alliance['logo'] = $xmlReader->getAttribute('logo', false);

                    $alliance['member'] = array();
                    break;
                case 'player':
                    $alliance['member'][] = $xmlReader->getAttribute('id');
                    break;
                default:
                    throw new UnknownElementException('alliance|player', $xmlReader->name);
            }
        }

        return [
            'server_id'       => $serverId,
            'last_update'     => $lastUpdate,
            'last_update_int' => $lastUpdateInt,
            'alliances'       => $alliances,
        ];
    }
}