<?php

namespace ChrKo\OStats\Task;


use ChrKo\OStats\XmlApi;

class XmlApiUpdate
{
    protected $dueTime = 0;

    protected $serverId = '';
    protected $endpoint = '';
    protected $category = 0;
    protected $type = 0;

    public function __construct($serverId, $endpoint, $category = 0, $type = 0, $dueTime = 0)
    {
        $this->serverId = (string)$serverId;
        $this->dueTime = (int)$dueTime;
        $this->endpoint = (string)$endpoint;
        $this->category = (int)$category;
        $this->type = (int)$type;

        $this->checkArguments();
    }

    public static function getAllowedArguments()
    {
        return [
            'alliances' => [
                'category' => [0],
                'type' => [0],
                'interval' => 24 * 60 * 60,
            ],
            'players' => [
                'category' => [0],
                'type' => [0],
                'interval' => 24 * 60 * 60,
            ],
            'universe' => [
                'category' => [0],
                'type' => [0],
                'interval' => 7 * 24 * 60 * 60,
            ],
            'highscore' => [
                'category' => [1, 2],
                'type' => range(0, 7),
                'interval' => 1 * 60 * 60,
            ],
        ];
    }

    public function run(XmlApi $xmlApi)
    {
        $this->checkArguments();

        $next = clone $this;
        switch ($this->endpoint) {
            case 'alliances':
                $data = $xmlApi->readAllianceData($this->serverId);
                $xmlApi->processAllianceData($data);
                break;
            case 'players':
                $data = $xmlApi->readPlayerData($this->serverId);
                $xmlApi->processPlayerData($data);
                break;
            case 'highscore':
                $data = $xmlApi->readHighscoreData($this->serverId, $this->category, $this->type);
                $xmlApi->processHighscoreData($data);
                break;
            case 'universe':
                $data = $xmlApi->readUniverseData($this->serverId);
                $xmlApi->processUniverseData($data);
                break;
            default:
                throw new \InvalidArgumentException;
        }

        $next->setDueTime($data['timestamp'] + $this->getAllowedArguments()[$this->getEndpoint()]['interval'] + 60);
        $next->save();
    }

    public function getServerId()
    {
        return $this->serverId;
    }

    public function setServerId($serverId)
    {
        $this->serverId = $serverId;
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function getDueTime()
    {
        return $this->dueTime;
    }

    public function setDueTime($dueTime)
    {
        $this->dueTime = $dueTime;
        return $this;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function save()
    {
        Scheduler::queue($this);
    }

    protected function checkArguments()
    {
        $args = self::getAllowedArguments();

        if (!array_key_exists($this->endpoint, $args)
            || !in_array($this->category, $args[$this->endpoint]['category'])
            || !in_array($this->type, $args[$this->endpoint]['type'])
        ) {
            throw new \InvalidArgumentException;
        }
    }
}