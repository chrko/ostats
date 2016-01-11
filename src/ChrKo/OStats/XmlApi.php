<?php

namespace ChrKo\OStats;

use ChrKo\XmlReaderProxy\Exceptions\UnknownElementException;
use ChrKo\XmlReaderProxy\Exceptions\XmlReaderExecption;
use ChrKo\XmlReaderProxy\XmlReader;
use ChrKo\OStats\BulkQuery;
use ChrKo\OStats\Task\XmlApiUpdate;

class XmlApi
{
    /**
     * @var BulkQuery\AbstractExecutor[]
     */
    protected $bulkQueries;

    public function __construct()
    {
        $this->bulkQueries = [];
        $this->bulkQueries['alliance'] = new BulkQuery\AllianceInsert(DB::getConn());
        $this->bulkQueries['member'] = new BulkQuery\AllianceMemberInsert(DB::getConn());
        $this->bulkQueries['player'] = new BulkQuery\PlayerInsert(DB::getConn());

        foreach (XmlApiUpdate::getAllowedArguments()['highscore']['category'] as $category) {
            foreach (XmlApiUpdate::getAllowedArguments()['highscore']['type'] as $type) {
                $this->bulkQueries['highscore_' . $category . '_' . $type] = new BulkQuery\HighscoreInsert(DB::getConn(), $category, $type);
            }
        }

        $this->bulkQueries['planet'] = new BulkQuery\PlanetInsert(DB::getConn());
        $this->bulkQueries['moon'] = new BulkQuery\MoonInsert(DB::getConn());
    }

    public static function getServerBaseById($serverId)
    {
        if (preg_match('/([a-z]{2})([0-9]{1,3})/', $serverId, $matches) !== 1) {
            throw new \Exception;
        };

        $base = "http://s${matches[2]}-${matches[1]}.ogame.gameforge.com/";

        return $base;
    }

    public static function getServerIdByBase($serverBase)
    {
        if (preg_match('/(http|https):\/\/s([0-9]{1,3})-([a-z]{2})\.ogame\.gameforge\.com/', $serverBase, $matches) !== 1) {
            throw new \Exception;
        };

        $serverId = "${matches[3]}${matches[2]}";

        return $serverId;
    }

    public static function readLocalServers($serverId)
    {
        $xml = new XmlReader();
        $xml->open(self::getServerBaseById($serverId) . 'api/universes.xml');

        $xml->read(true);

        if (!$xml->name == 'universes') {
            throw new UnknownElementException('universes', '');
        }

        $serverIds = [];

        while ($xml->read(false)) {
            if ($xml->nodeType !== XmlReader::ELEMENT) {
                continue;
            }

            $serverIds[] = self::getServerIdByBase($xml->getAttribute('href'));
        }

        return $serverIds;
    }

    public function flushBulkQueries()
    {
        foreach ($this->bulkQueries as $bulkQuery) {
            $bulkQuery->finish();
        }
    }

    public function readAllianceData($serverId)
    {
        $xmlReader = new XmlReader();
        $xmlReader->open(self::getServerBaseById($serverId) . 'api/alliances.xml');

        $xmlReader->read();
        if ($xmlReader->name != 'alliances') {
            throw new UnknownElementException('alliances', $xmlReader->name);
        }

        $timestamp = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $timestamp);

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
            'server_id' => $serverId,
            'last_update' => $lastUpdate,
            'timestamp' => $timestamp,
            'alliances' => $alliances
        ];
    }

    public function readHighscoreData($serverId, $category, $type)
    {
        $category = (string) $category;
        $type = (string) $type;

        if (
            !in_array($category, XmlApiUpdate::getAllowedArguments()['highscore']['category'])
            || !in_array($type, XmlApiUpdate::getAllowedArguments()['highscore']['type'])
        ) {
            throw new \Exception;
        }

        $categoryNames = [
            '1' => 'player',
            '2' => 'alliance',
        ];

        $xmlReader = new XmlReader();
        $xmlReader->open(
            self::getServerBaseById($serverId) . 'api/highscore.xml?'
            . '&category=' . $category
            . '&type=' . $type
        );

        $xmlReader->read();
        if ($xmlReader->name != 'highscore') {
            throw new UnknownElementException('highscore', $xmlReader->name);
        }

        $timestamp = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $timestamp);

        if (
            $xmlReader->getAttribute('category') != $category
            || $xmlReader->getAttribute('type') != $type
        ) {
            throw new XmlReaderExecption();
        }

        $highscore = [];

        while ($xmlReader->read(false)) {
            if ($xmlReader->nodeType !== XmlReader::ELEMENT || $xmlReader->name != $categoryNames[$category]) {
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
            'server_id' => $serverId,
            'last_update' => $lastUpdate,
            'timestamp' => $timestamp,
            'category' => $category,
            'type' => $type,
            'highscore' => $highscore,
        ];
    }

    public function readPlayersData($serverId)
    {
        $xmlReader = new XmlReader();
        $xmlReader->open(self::getServerBaseById($serverId) . 'api/players.xml');

        $xmlReader->read();
        if ($xmlReader->name != 'players') {
            throw new UnknownElementException('players', $xmlReader->name);
        }

        $timestamp = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $timestamp);

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
            'server_id' => $serverId,
            'last_update' => $lastUpdate,
            'timestamp' => $timestamp,
            'players' => $players
        ];
    }

    public function readUniverseData($serverId)
    {
        $xmlReader = new XmlReader();
        $xmlReader->open(self::getServerBaseById($serverId) . 'api/universe.xml');

        $xmlReader->read();
        if ($xmlReader->name != 'universe') {
            throw new UnknownElementException('universe', $xmlReader->name);
        }

        $timestamp = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $timestamp);

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
                        'id' => $xmlReader->getAttribute('id'),
                        'name' => $xmlReader->getAttribute('name'),
                        'galaxy' => $coords[0],
                        'system' => $coords[1],
                        'position' => $coords[2],
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
                        'id' => $xmlReader->getAttribute('id'),
                        'planet_id' => $lastPlanetId,
                        'size' => $xmlReader->getAttribute('size'),
                        'name' => $xmlReader->getAttribute('name'),
                    ];
                    break;
                default:
                    throw new UnknownElementException('planet|moon', $xmlReader->name);
            }
        }

        return [
            'server_id' => $serverId,
            'last_update' => $lastUpdate,
            'timestamp' => $timestamp,
            'planets' => $planets,
            'moons' => $moons,
        ];
    }

    public function processAllianceData(array $allianceData)
    {
        $serverId = $allianceData['server_id'];
        $lastUpdate = $allianceData['last_update'];

        $escape = function (&$value, $key) {
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
                    $value = '\'' . DB::getConn()->real_escape_string($value) . '\'';
                    break;
                case 'id':
                case 'alliance_id':
                case 'player_id':
                case 'open':
                    $value = (int) $value;
                    break;
            }
        };

        foreach ($allianceData['alliances'] as $alliance) {
            $alliance['server_id'] = $serverId;
            $alliance['last_update'] = $lastUpdate;
            array_walk($alliance, $escape);

            foreach ($alliance['member'] as $memberId) {
                $data = [
                    'server_id' => $allianceData['server_id'],
                    'alliance_id' => $alliance['id'],
                    'player_id' => $memberId,
                    'last_update' => $allianceData['last_update']
                ];
                array_walk($data, $escape);

                $this->bulkQueries['member']->run($data);
            }

            unset($alliance['member']);
            $this->bulkQueries['alliance']->run($alliance);
        }

        $this->flushBulkQueries();

        $this->bulkQueries['alliance']->clean($serverId, $lastUpdate);
        $this->bulkQueries['member']->clean($serverId, $lastUpdate);
    }

    public function processPlayersData(array $playerData)
    {
        $serverId = $playerData['server_id'];
        $lastUpdate = $playerData['last_update'];

        $escape = function (&$value, $key) {
            switch ($key) {
                case 'server_id':
                case 'name':
                case 'last_update':
                    $value = '\'' . DB::getConn()->real_escape_string($value) . '\'';
                    break;
                case 'id':
                case 'player_id':
                case 'alliance_id':
                case 'vacation':
                case 'inactive':
                case 'inactive_long':
                case 'banned':
                case 'outlaw':
                case 'admin':
                    $value = (int) $value;
                    break;
            }
        };

        foreach ($playerData['players'] as $player) {
            $player['server_id'] = $serverId;
            $player['last_update'] = $lastUpdate;

            $player['player_id'] = $player['id'];

            array_walk($player, $escape);

            $this->bulkQueries['player']->run($player);

            if ($player['alliance_id'] != 0) {
                $this->bulkQueries['member']->run($player);
            }
        }

        $this->flushBulkQueries();

        $this->bulkQueries['player']->clean($serverId, $lastUpdate);
        $this->bulkQueries['member']->clean($serverId, $lastUpdate);
    }

    public function processHighscoreData(array $highscoreData)
    {
        $serverId = '\'' . DB::getConn()->real_escape_string($highscoreData['server_id']) . '\'';
        $lastUpdate = '\'' . DB::getConn()->real_escape_string($highscoreData['last_update']) . '\'';
        $category = $highscoreData['category'];
        $type = $highscoreData['type'];

        $bulkQuery = $this->bulkQueries['highscore_' . $category . '_' . $type];

        foreach ($highscoreData['highscore'] as $row) {
            $row['server_id'] = $serverId;
            $row['seen'] = $lastUpdate;

            $bulkQuery->run($row);
        }

        $bulkQuery->finish()->clean($serverId, $lastUpdate);
    }

    public function processUniverseData(array $universeData)
    {
        $serverId = $universeData['server_id'];
        $lastUpdate = $universeData['last_update'];

        $escape = function (&$v, $k) {
            switch ($k) {
                case 'server_id':
                case 'name':
                case 'last_update':
                    $v = '\'' . DB::getConn()->real_escape_string($v) . '\'';
                    break;
                case 'id':
                case 'galaxy':
                case 'system':
                case 'position':
                case 'player_id':
                case 'size':
                    $v = (int) $v;
            }
        };

        foreach ($universeData['planets'] as $planet) {
            $planet['server_id'] = $serverId;
            $planet['last_update'] = $lastUpdate;

            array_walk($planet, $escape);
            $this->bulkQueries['planet']->run($planet);
        }

        foreach ($universeData['moons'] as $moon) {
            $moon['server_id'] = $serverId;
            $moon['last_update'] = $lastUpdate;

            array_walk($moon, $escape);
            $this->bulkQueries['moon']->run($moon);
        }

        $this->flushBulkQueries();

        $this->bulkQueries['planet']->clean($serverId, $lastUpdate);
        $this->bulkQueries['moon']->clean($serverId, $lastUpdate);
    }
}
