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
            case 'universe':
                $data = XML\Universe::readData($this->serverId);
                $xmlApiDataProcessor->processUniverseData($data);
                break;
            default:
                throw new \InvalidArgumentException;
        }

        $next->setDueTime($data['last_update_int'] + XML::getAllowedArguments()[$this->getEndpoint()]['interval'] + 60);
        $next->save();
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

    public function save() {
        Scheduler::queue($this);
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
