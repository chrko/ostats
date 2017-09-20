<?php

namespace ChrKo\OStats;

use ChrKo\OStats\BulkQuery;
use ChrKo\OStats\OGame\API\XML;
use ChrKo\OStats\Task\Scheduler;
use ChrKo\OStats\Task\XmlApiUpdate;

class XmlApiDataProcessor {
    /**
     * @var BulkQuery\AbstractExecutor[]
     */
    protected $bulkQueries;

    /**
     * @var XmlApiDataProcessor
     */
    private static $instance = false;

    private function __construct() {
        $this->bulkQueries = [];
        $this->bulkQueries['alliance'] = new BulkQuery\AllianceInsert(DB::getConn());
        $this->bulkQueries['member'] = new BulkQuery\AllianceMemberInsert(DB::getConn());
        $this->bulkQueries['player'] = new BulkQuery\PlayerInsert(DB::getConn());

        foreach (XML::getAllowedArguments()['highscore']['category'] as $category) {
            foreach (XML::getAllowedArguments()['highscore']['type'] as $type) {
                $this->bulkQueries['highscore_' . $category . '_' . $type] = new BulkQuery\HighscoreInsert(DB::getConn(), $category, $type);
            }
        }

        $this->bulkQueries['planet'] = new BulkQuery\PlanetInsert(DB::getConn());
        $this->bulkQueries['moon'] = new BulkQuery\MoonInsert(DB::getConn());

        $this->bulkQueries['tasks'] = new BulkQuery\ScheduleInsert(DB::getConn());
        $this->bulkQueries['tasks_force'] = new BulkQuery\ScheduleInsert(DB::getConn());
        $this->bulkQueries['tasks_force']->forceReschedule = true;
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function flushBulkQueries() {
        foreach ($this->bulkQueries as $bulkQuery) {
            $bulkQuery->finish();
        }
    }

    public function processAllianceData(array $allianceData) {
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
                    'server_id'       => $allianceData['server_id'],
                    'alliance_id'     => $alliance['id'],
                    'player_id'       => $memberId,
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

    public function processPlayerData(array $playerData) {
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

    public function processPlayersData(array $playersData) {
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
//        $this->bulkQueries['member']->clean($serverId, $lastUpdateInt);
    }

    public function processHighscoreData(array $highscoreData) {
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

    public function processServerData(array $serverData) {
        $insertSql = <<<SQL
INSERT IGNORE INTO `server_data` (
    `server_id`,
    `name`,
    `number`,
    `language`,
    `timezone`,
    `timezoneOffset`,
    `domain`,
    `version`,
    `speed`,
    `speedFleet`,
    `galaxies`,
    `systems`,
    `acs`,
    `rapidFire`,
    `defToTF`,
    `debrisFactor`,
    `debrisFactorDef`,
    `repairFactor`,
    `newbieProtectionLimit`,
    `newbieProtectionHigh`,
    `topScore`,
    `bonusFields`,
    `donutGalaxy`,
    `donutSystem`,
    `wfEnabled`,
    `wfMinimumRessLost`,
    `wfMinimumLossPercentage`,
    `wfBasicPercentageRepairable`,
    `globalDeuteriumSaveFactor`,
    `seen_int`
) VALUES (
    :server_id:,
    :name:,
    :number:,
    :language:,
    :timezone:,
    :timezoneOffset:,
    :domain:,
    :version:,
    :speed:,
    :speedFleet:,
    :galaxies:,
    :systems:,
    :acs:,
    :rapidFire:,
    :defToTF:,
    :debrisFactor:,
    :debrisFactorDef:,
    :repairFactor:,
    :newbieProtectionLimit:,
    :newbieProtectionHigh:,
    :topScore:,
    :bonusFields:,
    :donutGalaxy:,
    :donutSystem:,
    :wfEnabled:,
    :wfMinimumRessLost:,
    :wfMinimumLossPercentage:,
    :wfBasicPercentageRepairable:,
    :globalDeuteriumSaveFactor:,
    :seen_int:
);
SQL;
        $escape = function (&$v, $k) {
            switch ($k) {
                case 'server_id':
                case 'name':
                case 'language':
                case 'timezone':
                case 'domain':
                case 'version':
                    $v = '\'' . DB::getConn()->real_escape_string($v) . '\'';
                    break;
                case 'timezoneOffset':
                case 'number':
                case 'speed':
                case 'speedFleet':
                case 'galaxies':
                case 'systems':
                case 'newbieProtectionLimit':
                case 'newbieProtectionHigh':
                case 'topScore':
                case 'bonusFields':
                case 'wfMinimumRessLost':
                case 'wfMinimumLossPercentage':
                case 'wfBasicPercentageRepairable':
                case 'seen_int':
                    $v = (string) (int) $v;
                    break;
                case 'acs':
                case 'rapidFire':
                case 'defToTF':
                case 'donutGalaxy':
                case 'donutSystem':
                case 'wfEnabled':
                    $v = (string) ((bool) $v) ? 1 : 0;
                    break;
                case 'debrisFactor':
                case 'debrisFactorDef':
                case 'repairFactor':
                case 'globalDeuteriumSaveFactor':
                    $v = (string) (double) $v;
                    break;
            }
        };

        $data = $serverData['server_data'];
        $data['server_id'] = $serverData['server_id'];
        $data['seen_int'] = $serverData['last_update_int'];

        array_walk($data, $escape);

        if (!array_key_exists('name', $data)) {
            $data['name'] = 'NULL';
        }

        $sql = DB::namedReplace($insertSql, $data);
        DB::getConn()->query($sql);
    }

    public function processUniverseData(array $universeData) {
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

    public function processUniversesData(array $universesData) {
        $ownServerId = $universesData['server_id'];
        $lastUpdateInt = $universesData['last_update_int'];

        $serverIds = $universesData['server_ids'];

        foreach ($serverIds as $serverId) {
            $task = new XmlApiUpdate($serverId, 'universes', 0, 0, 0);
            if ($serverId == $ownServerId) {
                if ($lastUpdateInt + XML\Universes::UPDATE_INTERVAL > time() + floor(XML\Universes::UPDATE_INTERVAL / 2)) {
                    $nextDueTime = $lastUpdateInt + XML\Universes::UPDATE_INTERVAL;
                } else {
                    $nextDueTime = time() + XML\Universes::UPDATE_INTERVAL;
                }
                $task->setDueTime($nextDueTime);
                $this->bulkQueries['tasks_force']->run(Scheduler::prepare($task));
            } else {
                $this->bulkQueries['tasks']->run(Scheduler::prepare($task));
            }

            foreach (XML::getAllowedArguments() as $endpoint => $details) {
                $categories = $details['category'];
                $types = $details['type'];
                foreach ($categories as $category) {
                    foreach ($types as $type) {
                        $this->bulkQueries['tasks']->run(Scheduler::prepare(
                            new XmlApiUpdate($serverId, $endpoint, $category, $type)
                        ));
                    }
                }
            }
        }

        $this->flushBulkQueries();
    }
}
