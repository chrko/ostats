<?php

use ChrKo\XMLReaderProxy;

function signals()
{
    return [
        SIGINT => 'SIGINT',
        SIGTERM => 'SIGTERM',
    ];
}

function sig_handler($sig)
{
    $signals = signals();

    foreach ($signals as $signal => $signalName) {
        echo $signal == $sig ? $signalName : '';
    }
    echo "\n";
    switch ($sig) {
        case SIGTERM:
        case SIGINT:
            \ChrKo\Scheduler::end();
            break;
        default:
            echo $sig;
    }

}

function getServerBaseById($server_id)
{
    if (preg_match('/([a-z]{2})([0-9]{1,3})/', $server_id, $matches) !== 1) {
        throw new Exception;
    };

    $prefix = "http://s${matches[2]}-${matches[1]}.ogame.gameforge.com/";

    return $prefix;
}

function readPlayerData($serverBase, $cache = false)
{
    $xml = new XMLReaderProxy();
    $url = $serverBase . '/api/players.xml';
    $xml->open($url);

    $xml->read(true);
    if (!$xml->name == 'players')
        throw new Exception;

    $timestamp = (int)$xml->getAttribute('timestamp');
    $last_update = date('Y-m-d H:i:s', $timestamp);

    $server_id = $xml->getAttribute('serverId');

    if ($cache) {
        file_put_contents("cache/players.${serverId}.${timestamp}.xml", file_get_contents($url));
    }

    $players = array();

    while ($xml->read()) {
        if ($xml->nodeType != XMLReaderProxy::ELEMENT)
            continue;

        $player = array();

        $player['id'] = $xml->getAttribute('id');
        $player['name'] = $xml->getAttribute('name');

        $statusString = $xml->getAttribute('status', '');

        $player['admin'] = strpos($statusString, 'a') !== false;
        $player['vacation'] = strpos($statusString, 'v') !== false;
        $player['inactive_long'] = strpos($statusString, 'I') !== false;
        $player['inactive'] = $player['inactive_long'] || strpos($statusString, 'i') !== false;
        $player['outlaw'] = strpos($statusString, 'o') !== false;
        $player['banned'] = strpos($statusString, 'b') !== false;

        $player['alliance_id'] = $xml->getAttribute('alliance', false);

        $players[$player['id']] = $player;
    }

    $due_time = $timestamp + 24 * 60 * 60 + 60;

    \ChrKo\Scheduler::schedule(
        \ChrKo\DB::formatTimestamp($due_time),
        $server_id,
        'players'
    );

    return [
        'server_id' => $server_id,
        'last_update' => $last_update,
        'timestamp' => $timestamp,
        'players' => $players
    ];
}

function readAllianceData($serverBase, $cache = false)
{
    $url = $serverBase . '/api/alliances.xml';

    $xml = new XMLReaderProxy();

    $xml->open($url);
    $xml->read(true);

    $xml->name == 'alliances' or die('test');

    $timestamp = (int)$xml->getAttribute('timestamp');
    $last_update = date('Y-m-d H:i:s', $timestamp);

    $server_id = $xml->getAttribute('serverId');

    if ($cache) {
        file_put_contents("cache/alliances.${serverId}.${timestamp}.xml", file_get_contents($url));
    }

    $alliances = array();
    $alliance = array();

    while ($xml->read()) {
        if ($xml->nodeType !== XMLReaderProxy::ELEMENT) {
            continue;
        }

        if ($xml->name == 'alliance') {
            if (count($alliance) > 0) {
                $alliances[$alliance['id']] = $alliance;
                $alliance = array();
            }
            $alliance['id'] = $xml->getAttribute('id');
            $alliance['name'] = $xml->getAttribute('name');
            $alliance['tag'] = $xml->getAttribute('tag');
            $alliance['open'] = boolval($xml->getAttribute('open', false));

            $alliance['homepage'] = $xml->getAttribute('homepage', false);
            $alliance['logo'] = $xml->getAttribute('logo', false);

            $alliance['member'] = array();
        }
        if ($xml->name == 'player') {
            $alliance['member'][] = $xml->getAttribute('id');
        }
    }

    $due_time = $timestamp + 24 * 60 * 60 + 60;

    \ChrKo\Scheduler::schedule(
        \ChrKo\DB::formatTimestamp($due_time),
        $server_id,
        'alliances'
    );

    return [
        'server_id' => $server_id,
        'last_update' => $last_update,
        'timestamp' => $timestamp,
        'alliances' => $alliances
    ];
}

function bulkUpdatePlayerData($playerData)
{
    $updater = new \ChrKo\PlayerUpdater();
    foreach ($playerData['players'] as $player) {
        $player['server_id'] = $playerData['server_id'];
        $player['last_update'] = $playerData['last_update'];

        array_walk($player, function (&$value, $key) {
            switch ($key) {
                case 'server_id':
                case 'name':
                case 'last_update':
                    $value = '\'' . \ChrKo\DB::getConn()->real_escape_string($value) . '\'';
                    break;
                case 'id':
                case 'vacation':
                case 'inactive':
                case 'inactive_long':
                case 'banned':
                case 'outlaw':
                case 'admin':
                    $value = (int)$value;
                    break;
            }
        });

        $updater->run($player);
    }

    $updater->finish();

    $updater->clean($playerData['server_id'], $playerData['last_update']);

}

function bulkUpdateAllianceData($allianceData)
{
    $updater = new \ChrKo\AllianceUpdater();

    foreach ($allianceData['alliances'] as $alliance) {

        $alliance['last_update'] = $allianceData['last_update'];
        $alliance['server_id'] = $allianceData['server_id'];
        array_walk($alliance, function (&$value, $key) {
            switch ($key) {
                case 'homepage':
                case 'logo':
                    if ($value === false) {
                        $value = 'NULL';
                        break;
                    }
                case 'server_id':
                case 'name':
                case 'tag':
                case 'last_update':
                    $value = '\'' . \ChrKo\DB::getConn()->real_escape_string($value) . '\'';
                    break;
                case 'id':
                case 'open':
                    $value = (int)$value;
                    break;
            }
        });

        unset($alliance['member']);

        $updater->run($alliance);
    }

    $updater->finish();

}

function bulkUpdateAllianceMemberByAllianceData($allianceData)
{
    $updater = new \ChrKo\AllianceMemberUpdater();

    foreach ($allianceData['alliances'] as $alliance) {
        foreach ($alliance['member'] as $member_id) {
            $data = [
                'server_id' => '\'' . \ChrKo\DB::getConn()->real_escape_string($allianceData['server_id']) . '\'',
                'alliance_id' => $alliance['id'],
                'player_id' => $member_id,
                'last_update' => '\'' . \ChrKo\DB::getConn()->real_escape_string($allianceData['last_update']) . '\''
            ];

            $updater->run($data);
        }
    }

    $updater->finish();
}

function bulkUpdateAllianceMemberByPlayerData($playerData)
{
    $updater = new \ChrKo\AllianceMemberUpdater();

    foreach ($playerData['players'] as $player) {
        if ($player['alliance_id'] === false) {
            continue;
        }

        $data = [
            'server_id' => '\'' . \ChrKo\DB::getConn()->real_escape_string($playerData['server_id']) . '\'',
            'alliance_id' => $player['alliance_id'],
            'player_id' => $player['id'],
            'last_update' => '\'' . \ChrKo\DB::getConn()->real_escape_string($playerData['last_update']) . '\''
        ];

        $updater->run($data);
    }

    $updater->finish();
}

function bulkUpdateHighscore($serverBase, array $categories = null, array $types = null, $cache = false)
{
    if ($categories === null) {
        $categories = [
            '1',
            '2',
        ];
    }

    $categoriesKeys = [
        '1' => '1',
        '2' => '2',
        'player' => '1',
        'alliance' => '2'
    ];
    $categoriesValues = [
        '1' => 'player',
        '2' => 'alliance'
    ];

    $categoriesNormalized = [];

    foreach ($categories as $value) {
        $categoriesNormalized[$categoriesKeys[(string)$value]] = $categoriesValues[$categoriesKeys[(string)$value]];
    }

    $typesValues = [
        '0' => 'total',
        '1' => 'economy',
        '2' => 'research',
        '3' => 'military',
        '4' => 'military_lost',
        '5' => 'military_built',
        '6' => 'military_destroyed',
        '7' => 'honor',
    ];

    $typesKeys = [
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        'total' => '0',
        'economy' => '1',
        'research' => '2',
        'military' => '3',
        'military_lost' => '4',
        'military_built' => '5',
        'military_destroyed' => '6',
        'honor' => '7',
    ];

    if ($types === null) {
        $types = $typesValues;
    }

    foreach ($types as $value) {
        $typesNormalized[$typesKeys[(string)$value]] = $typesValues[$typesKeys[(string)$value]];
    }

    foreach ($categoriesNormalized as $categoryId => $categoryName) {
        $prefix = $serverBase . '/api/highscore.xml?category=' . $categoryId . '&type=';

        $updater = new \ChrKo\HighscoreUpdater($categoryName);

        foreach ($types as $typeId => $type) {
            $url = $prefix . $typeId;
            $xml = new XMLReaderProxy();
            $xml->open($url);
            $xml->read(true);
            if (!$xml->name == 'highscore') {
                throw new Exception;
            }

            $server_id = $xml->getAttribute('serverId');

            $timestamp = (int)$xml->getAttribute('timestamp');
            $last_update = date('Y - m - d H:i:s', $timestamp);

            if ($cache) {
                file_put_contents("cache/highscore.${categoryId}.${typeId}.${timestamp}.xml", file_get_contents($url));
            }

            while ($xml->read()) {
                $data = [];
                if (!$xml->nodeType == XMLReaderProxy::ELEMENT || $xml->name != $categoryName)
                    continue;
                $data['server_id'] = $server_id;
                $data[$categoryName . '_id'] = $xml->getAttribute('id');
                $data['type'] = $typeId;
                $data['points'] = $xml->getAttribute('score');
                $data['rank'] = $xml->getAttribute('position');
                $data['seen'] = $last_update;

                array_walk($data, function (&$v) {
                    $v = '\'' . \ChrKo\DB::getConn()->real_escape_string($v) . '\'';
                });
                $updater->run($data);
            }

            $due_time = $timestamp + 60 * 60 + 60;

            \ChrKo\Scheduler::schedule(
                \ChrKo\DB::formatTimestamp($due_time),
                $server_id,
                'highscore',
                $categoryId,
                $typeId
            );
        }

        $updater->finish();
    }
}

function bulkUpdateUniverse($serverBase, $cache = false)
{
    $url = $serverBase . '/api/universe.xml';

    $xml = new XMLReaderProxy();
    $xml->open($url);
    $xml->read(true);

    if (!$xml->name == 'universe') {
        throw new Exception;
    }

    $server_id = $xml->getAttribute('serverId');

    $timestamp = (int)$xml->getAttribute('timestamp');
    $last_update = date('Y-m-d H:i:s', $timestamp);

    $planetUpdater = new \ChrKo\PlanetUpdater();
    $moonUpdater = new \ChrKo\MoonUpdater();

    $last_planet_id = 0;

    $escape = function (&$v, $k) {
        switch ($k) {
            case 'server_id':
            case 'name':
            case 'last_update':
                $v = '\'' . \ChrKo\DB::getConn()->real_escape_string($v) . '\'';
                break;
            case 'id':
            case 'galaxy':
            case 'system':
            case 'position':
            case 'player_id':
                $v = (int)$v;
        }
    };

    while ($xml->read()) {
        if (!$xml->nodeType == XMLReaderProxy::ELEMENT)
            continue;

        if ($xml->name == 'planet') {
            $coords = explode(':', $xml->getAttribute('coords'));
            $data = [
                'server_id' => $server_id,
                'id' => $xml->getAttribute('id'),
                'name' => $xml->getAttribute('name'),
                'galaxy' => $coords[0],
                'system' => $coords[1],
                'position' => $coords[2],
                'player_id' => $xml->getAttribute('player'),
                'last_update' => $last_update
            ];

            $last_planet_id = $data['id'];

            array_walk($data, $escape);
            $planetUpdater->run($data);
        }
        if ($xml->name == 'moon') {
            $data = [
                'server_id' => $server_id,
                'id' => $xml->getAttribute('id'),
                'planet_id' => $last_planet_id,
                'size' => $xml->getAttribute('size'),
                'name' => $xml->getAttribute('name'),
                'last_update' => $last_update
            ];
            array_walk($data, $escape);

            $moonUpdater->run($data);
        }
    }

    $planetUpdater->finish();
    $moonUpdater->finish();
    $planetUpdater->clean($server_id, $last_update);
    $moonUpdater->clean($server_id, $last_update);

    $due_time = $timestamp + 7 * 24 * 60 * 60 + 60;

    \ChrKo\Scheduler::schedule(
        \ChrKo\DB::formatTimestamp($due_time),
        $server_id,
        'universe'
    );
}
