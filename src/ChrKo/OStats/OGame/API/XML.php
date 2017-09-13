<?php

namespace ChrKo\OStats\OGame\API;

use ChrKo\OStats\OGame\API\XML\Highscore;

class XML {
    private static $endpoints = [
        'alliances',
        'highscore',
        'player',
        'players',
        'universe',
        'universes',
    ];

    public static function getServerBaseById($serverId) {
        if (preg_match('/([a-z]{2})([0-9]{1,3})/', $serverId, $matches) !== 1) {
            throw new \Exception;
        };

        $base = "http://s${matches[2]}-${matches[1]}.ogame.gameforge.com/";

        return $base;
    }

    public static function getServerIdByBase($serverBase) {
        if (preg_match('/(http|https):\/\/s([0-9]{1,3})-([a-z]{2})\.ogame\.gameforge\.com/', $serverBase, $matches) !== 1) {
            throw new \Exception;
        };

        $serverId = "${matches[3]}${matches[2]}";

        return $serverId;
    }

    public static function getAllowedArguments() {
        $args = [];
        foreach (self::$endpoints as $endpoint) {
            $class = '\ChrKo\OStats\OGame\API\XML\\' . ucfirst($endpoint);
            try {
                $tmp = $class::getAllowedArguments();
            } catch (\Error $e) {
                $tmp = [
                    'category' => [0],
                    'type'     => [0],
                ];
            }
            $tmp['interval'] = $class::UPDATE_INTERVAL;

            $args[$endpoint] = $tmp;
        }

        return $args;
    }
}