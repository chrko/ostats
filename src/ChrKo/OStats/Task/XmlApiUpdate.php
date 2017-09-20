<?php

namespace ChrKo\OStats\Task;


use ChrKo\OStats\BulkQuery\ScheduleInsert;
use ChrKo\OStats\DB;
use ChrKo\OStats\OGame\API\XML;
use ChrKo\OStats\XmlApiDataProcessor;

class XmlApiUpdate implements TaskInterface {
    protected $dueTime = 0;

    protected $serverId = '';
    protected $endpoint = '';
    protected $category = 0;
    protected $type = 0;

    public function __construct($serverId, $endpoint, $category = 0, $type = 0, $dueTime = 0) {
        $this->serverId = (string) $serverId;
        $this->dueTime = (int) $dueTime;
        $this->endpoint = (string) $endpoint;
        $this->category = (int) $category;
        $this->type = (int) $type;

        if ($this->dueTime == 0) {
            $this->dueTime = time();
        }

        $this->checkArguments();
    }

    public function run() {
        $xmlApiDataProcessor = XmlApiDataProcessor::getInstance();

        $this->checkArguments();

        $next = clone $this;
        switch ($this->endpoint) {
            case 'alliances':
                $data = XML\Alliances::readData($this->serverId);
                $xmlApiDataProcessor->processAllianceData($data);
                break;
            case 'player':
                try {
                    $data = XML\Player::readData($this->serverId, $this->type);
                } catch (\Exception $e) {
                    return;
                }
                $xmlApiDataProcessor->processPlayerData($data);
                break;
            case 'players':
                $data = XML\Players::readData($this->serverId);
                if (!DISABLE_PLAYER) {
                    $bulkQuery = new ScheduleInsert(DB::getConn());
                    foreach ($data['players'] as $player) {
                        $bulkQuery->run(
                            Scheduler::prepare(
                                new self($data['server_id'], 'player', 0, $player['id'])
                            )
                        );
                    }
                    $bulkQuery->finish();
                }
                $xmlApiDataProcessor->processPlayersData($data);
                break;
            case 'highscore':
                $data = XML\Highscore::readData($this->serverId, $this->category, $this->type);
                $xmlApiDataProcessor->processHighscoreData($data);
                break;
            case 'serverData':
                $data = XML\ServerData::readData($this->serverId);
                $xmlApiDataProcessor->processServerData($data);
                break;
            case 'universe':
                $data = XML\Universe::readData($this->serverId);
                $xmlApiDataProcessor->processUniverseData($data);
                break;
            case 'universes':
                $data = XML\Universes::readData($this->serverId);
                $xmlApiDataProcessor->processUniversesData($data);
                break;
            default:
                throw new \InvalidArgumentException;
        }

        $lastUpdateInt = $data['last_update_int'];
        $interval = XML::getAllowedArguments()[$this->getEndpoint()]['interval'] + 60;
        if ($next->endpoint != 'serverData'
            || ($lastUpdateInt + $interval) > (time() + floor($interval / 2))
        ) {
            $nextDueTime = $lastUpdateInt + $interval;
        } else {
            $nextDueTime = time() + $interval;
        }

        $next->setDueTime($nextDueTime);
        Scheduler::queue($next);
    }

    public function getServerId() {
        return $this->serverId;
    }

    public function setServerId($serverId) {
        $this->serverId = $serverId;

        return $this;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setCategory($category) {
        $this->category = $category;

        return $this;
    }

    public function getDueTime() {
        return $this->dueTime;
    }

    public function setDueTime($dueTime) {
        $this->dueTime = $dueTime;

        return $this;
    }

    public function getEndpoint() {
        return $this->endpoint;
    }

    public function setEndpoint($endpoint) {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    public function getSlug() {
        return sprintf(
            '%s-%s-%u-%u',
            $this->getServerId(),
            $this->getEndpoint(),
            $this->getCategory(),
            $this->getType()
        );
    }

    public function getJobType() {
        return 'xml-' . $this->getEndpoint();
    }

    protected function checkArguments() {
        $args = XML::getAllowedArguments();

        if (!array_key_exists($this->endpoint, $args)
            || !in_array($this->category, $args[$this->endpoint]['category'])
            || (!in_array($this->type, $args[$this->endpoint]['type']) && $this->endpoint != 'player')
        ) {
            throw new \InvalidArgumentException;
        }
    }
}
