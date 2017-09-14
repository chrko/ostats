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

class Universes {
    const UPDATE_INTERVAL = 6 * 60 * 60;

    public static function readData($serverId) {
        $xml = new XmlReader();
        $xml->open(XML::getServerBaseById($serverId) . 'api/universes.xml');

        $xml->read(true);

        if (!$xml->name == 'universes') {
            throw new UnknownElementException('universes', '');
        }
        $lastUpdateInt = (int) $xml->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $lastUpdateInt);

        $serverIds = [];

        while ($xml->read(false)) {
            if ($xml->nodeType !== XmlReader::ELEMENT) {
                continue;
            }

            $serverIds[] = XML::getServerIdByBase($xml->getAttribute('href'));
        }

        return [
            'server_id'       => $serverId,
            'last_update'     => $lastUpdate,
            'last_update_int' => $lastUpdateInt,
            'server_ids'      => $serverIds,
        ];
    }
}