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
            'server_id' => $serverId,
            'last_update' => $lastUpdate,
            'last_update_int' => $lastUpdateInt,
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
            'last_update_int' => $lastUpdateInt,
            'category' => $category,
            'type' => $type,
            'highscore' => $highscore,
        ];
    }

    public function readPlayerData($serverId, $playerId)
    {
        $xmlReader = new XmlReader();
        $xmlReader->open(self::getServerBaseById($serverId) . 'api/playerData.xml?id=' . $playerId);

        $xmlReader->read();
        if ($xmlReader->name != 'playerData') {
            throw new UnknownElementException('playerData', $xmlReader->name);
        }

        $lastUpdateInt = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $lastUpdateInt);

        $data = [
            'server_id' => $serverId,
            'player_id' => $playerId,
            'last_update' => $lastUpdate,
            'last_update_int' => $lastUpdateInt,
            'highscore' => [],
            'planets' => [],
            'moons' => [],
            'alliance_id' => 0,
            'alliance_name' => '',
            'alliance_tag' => '',
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
                        'id' => $playerId,
                        'type' => (int) $xmlReader->getAttribute('type'),
                        'position' => (int) $xmlReader->readString(),
                        'points' => (int) $xmlReader->getAttribute('score'),
                        'ships' => (int) $xmlReader->getAttribute('ships', 0),
                    ];
                    break;
                case 'planet':
                    $coords = explode(':', $xmlReader->getAttribute('coords'));
                    $data['planets'][] = [
                        'server_id' => $serverId,
                        'last_update_int' => $lastUpdateInt,
                        'id' => $xmlReader->getAttribute('id'),
                        'name' => $xmlReader->getAttribute('name'),
                        'galaxy' => $coords[0],
                        'system' => $coords[1],
                        'position' => $coords[2],
                        'player_id' => $playerId,
                    ];

                    $lastPlanetId = $xmlReader->getAttribute('id');
                    break;
                case 'moon':
                    if ($lastPlanetId === 0) {
                        throw new \Exception;
                    }
                    $data['moons'][] = [
                        'server_id' => $serverId,
                        'last_update_int' => $lastUpdateInt,
                        'id' => $xmlReader->getAttribute('id'),
                        'planet_id' => $lastPlanetId,
                        'size' => $xmlReader->getAttribute('size'),
                        'name' => $xmlReader->getAttribute('name'),
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

    public function readPlayersData($serverId)
    {
        $xmlReader = new XmlReader();
        $xmlReader->open(self::getServerBaseById($serverId) . 'api/players.xml');

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
            'server_id' => $serverId,
            'last_update' => $lastUpdate,
            'last_update_int' => $lastUpdateInt,
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
            'last_update_int' => $lastUpdateInt,
            'planets' => $planets,
            'moons' => $moons,
        ];
    }

    public function processAllianceData(array $allianceData)
    {
        $serverId = $allianceData['server_id'];
        $lastUpdateInt = $allianceData['last_update_int'];

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
                    $value = '\'' . DB::getConn()->real_escape_string($value) . '\'';
                    break;
                case 'id':
                case 'alliance_id':
                case 'player_id':
                case 'open':
                case 'last_update_int':
                    $value = (int) $value;
                    break;
            }
        };

        foreach ($allianceData['alliances'] as $alliance) {
            $alliance['server_id'] = $serverId;
            $alliance['last_update_int'] = $lastUpdateInt;
            array_walk($alliance, $escape);

            foreach ($alliance['member'] as $memberId) {
                $data = [
                    'server_id' => $allianceData['server_id'],
                    'alliance_id' => $alliance['id'],
                    'player_id' => $memberId,
                    'last_update_int' => $lastUpdateInt,
                ];
                array_walk($data, $escape);

                $this->bulkQueries['member']->run($data);
            }

            unset($alliance['member']);
            $this->bulkQueries['alliance']->run($alliance);
        }

        $this->flushBulkQueries();

        $this->bulkQueries['alliance']->clean($serverId, $lastUpdateInt);
        $this->bulkQueries['member']->clean($serverId, $lastUpdateInt);
    }

    public function processPlayerData(array $playerData)
    {
        $escape = function (&$v, $k) {
            switch ($k) {
                case 'server_id':
                case 'name':
                case 'player_name':
                case 'alliance_name':
                case 'alliance_tag':
                    $v = '\'' . DB::getConn()->real_escape_string($v) . '\'';
                    break;
                case 'id':
                case 'player_id':
                case 'alliance_id':
                case 'galaxy':
                case 'system':
                case 'position':
                case 'size':
                case 'last_update_int':
                    $v = (int) $v;
            }
        };

        foreach ($playerData['planets'] as $planet) {
            array_walk($planet, $escape);
            $this->bulkQueries['planet']->run($planet);
        }
        unset($playerData['planets'], $planet);

        foreach ($playerData['moons'] as $moon) {
            array_walk($moon, $escape);
            $this->bulkQueries['moon']->run($moon);
        }
        unset($playerData['moons'], $moon);

        $serverId = '\'' . DB::getConn()->real_escape_string($playerData['server_id']) . '\'';

        foreach ($playerData['highscore'] as $point) {
            $point['server_id'] = $serverId;
            $point['seen_int'] = $playerData['last_update_int'];
            $this->bulkQueries['highscore_1_' . $point['type']]->run($point);
        }
        unset($playerData['highscore'], $point);

        array_walk($playerData, $escape);

        $query = 'INSERT INTO `player` (`server_id`, `id`, `name`, `last_update_int`)'
            . ' VALUES (:server_id:, :player_id:, :player_name:, :last_update_int:)'
            . ' ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `last_update_int` = VALUES(`last_update_int`)';

        $query = DB::namedReplace($query, $playerData);
        DB::getConn()->query($query);

        if ($playerData['alliance_id'] !== 0) {
            $this->bulkQueries['member']->run($playerData);

            $query = 'INSERT INTO `alliance` (`server_id`, `id`, `name`, `tag`, `last_update_int`)'
                . ' VALUES (:server_id:, :alliance_id:, :alliance_name:, :alliance_tag:, :last_update_int:)'
                . ' ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `tag` = VALUES(`tag`), `last_update_int` = VALUES(`last_update_int`)';
        } else {
            $query = 'DELETE FROM `alliance_member`'
                . ' WHERE `server_id` = :server_id: AND `player_id` = :player_id:';
        }
        $query = DB::namedReplace($query, $playerData);
        DB::getConn()->query($query);


        $this->flushBulkQueries();
    }

    public function processPlayersData(array $playersData)
    {
        $serverId = $playersData['server_id'];
        $lastUpdateInt = $playersData['last_update_int'];

        $escape = function (&$value, $key) {
            switch ($key) {
                case 'server_id':
                case 'name':
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
                case 'last_update_int':
                    $value = (int) $value;
                    break;
            }
        };

        foreach ($playersData['players'] as $player) {
            $player['server_id'] = $serverId;
            $player['last_update_int'] = $lastUpdateInt;

            $player['player_id'] = $player['id'];

            array_walk($player, $escape);

            $this->bulkQueries['player']->run($player);

//            if ($player['alliance_id'] != 0) {
//                $this->bulkQueries['member']->run($player);
//            }
        }

        $this->flushBulkQueries();

        $this->bulkQueries['player']->clean($serverId, $lastUpdateInt);
        $this->bulkQueries['member']->clean($serverId, $lastUpdateInt);
    }

    public function processHighscoreData(array $highscoreData)
    {
        $serverId = '\'' . DB::getConn()->real_escape_string($highscoreData['server_id']) . '\'';
        $lastUpdateInt = $highscoreData['last_update_int'];
        $category = $highscoreData['category'];
        $type = $highscoreData['type'];

        $bulkQuery = $this->bulkQueries['highscore_' . $category . '_' . $type];

        foreach ($highscoreData['highscore'] as $row) {
            $row['server_id'] = $serverId;
            $row['seen_int'] = $lastUpdateInt;

            $bulkQuery->run($row);
        }

        $bulkQuery->finish()->clean($serverId, $lastUpdateInt);
    }

    public function processUniverseData(array $universeData)
    {
        $serverId = $universeData['server_id'];
        $lastUpdateInt = $universeData['last_update_int'];

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
                case 'last_update_int':
                    $v = (int) $v;
            }
        };

        foreach ($universeData['planets'] as $planet) {
            $planet['server_id'] = $serverId;
            $planet['last_update_int'] = $lastUpdateInt;

            array_walk($planet, $escape);
            $this->bulkQueries['planet']->run($planet);
        }

        foreach ($universeData['moons'] as $moon) {
            $moon['server_id'] = $serverId;
            $moon['last_update_int'] = $lastUpdateInt;

            array_walk($moon, $escape);
            $this->bulkQueries['moon']->run($moon);
        }

        $this->flushBulkQueries();

        $this->bulkQueries['planet']->clean($serverId, $lastUpdateInt);
        $this->bulkQueries['moon']->clean($serverId, $lastUpdateInt);
    }
}
