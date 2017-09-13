<?php

namespace ChrKo\OStats\OGame\API\XML;


use ChrKo\OStats\OGame\API\XML;
use ChrKo\XmlReaderProxy\Exceptions\UnknownElementException;
use ChrKo\XmlReaderProxy\Exceptions\XmlReaderExecption;
use ChrKo\XmlReaderProxy\XmlReader;

class Highscore {

    const UPDATE_INTERVAL = 1 * 60 * 60;

    private static $categories = [
        1 => 'player',
        2 => 'alliance',
    ];

    private static $types = [
        0 => 'total',
        1 => 'economy',
        2 => 'research',
        3 => 'military',
        4 => 'military_lost',
        5 => 'military_built',
        6 => 'military_destroyed',
        7 => 'honor',
    ];

    public static function getAllowedArguments() {
        return [
            'category' => array_keys(self::$categories),
            'type'     => array_keys(self::$types),
        ];
    }

    public static function validateParameters(int $category, int $type, bool $exception = true) {
        if (
            !array_key_exists((int) $category, self::$categories)
            || !array_key_exists((int) $type, self::$types)
        ) {
            if ($exception) {
                throw new \Exception;
            }
            return false;
        }

        return true;
    }

    public static function readData(string $serverId, int $category, int $type) {
        self::validateParameters($category, $type);

        $category = (string) $category;
        $type = (string) $type;

        $xmlReader = new XmlReader();
        $xmlReader->open(
            XML::getServerBaseById($serverId) . 'api/highscore.xml?'
            . '&category=' . $category
            . '&type=' . $type
        );

        $xmlReader->read();
        if ($xmlReader->name != 'highscore') {
            throw new UnknownElementException('highscore', $xmlReader->name);
        }

        $lastUpdateInt = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $lastUpdateInt);

        if (
            $xmlReader->getAttribute('category') != $category
            || $xmlReader->getAttribute('type') != $type
        ) {
            throw new XmlReaderExecption();
        }

        $highscore = [];

        while ($xmlReader->read(false)) {
            if ($xmlReader->nodeType !== XmlReader::ELEMENT || $xmlReader->name != self::$categories[(int) $category]) {
                continue;
            }
            $row = [];

            $row['id'] = (int) $xmlReader->getAttribute('id');
            $row['points'] = (int) $xmlReader->getAttribute('score');
            $row['position'] = (int) $xmlReader->getAttribute('position');
            $row['ships'] = (int) $xmlReader->getAttribute('ships', 0);

            $highscore[] = $row;
        }

        return [
            'server_id'       => $serverId,
            'last_update'     => $lastUpdate,
            'last_update_int' => $lastUpdateInt,
            'category'        => $category,
            'type'            => $type,
            'highscore'       => $highscore,
        ];
    }
}