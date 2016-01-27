<?php

namespace ChrKo\OStats\Task;

interface TaskInterface
{
    /**
     * @return int
     */
    public function getDueTime();

    /**
     * @return string
     */
    public function getServerId();

    /**
     * @return string
     */
    public function getEndpoint();

    /**
     * @return int
     */
    public function getCategory();

    /**
     * @return int
     */
    public function getType();
}
