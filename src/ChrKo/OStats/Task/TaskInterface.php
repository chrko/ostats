<?php

namespace ChrKo\OStats\Task;

interface TaskInterface {
    public function getDueTime();

    public function getJobType();

    /**
     * @return string
     */
    public function getSlug();

    public function run();
}
