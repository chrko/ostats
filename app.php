#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

use ChrKo\OStats\Command\WorkerCommand;
use ChrKo\OStats\Command\CreateServerCommand;
use ChrKo\OStats\Command\StatusCommand;

$app = new Application();
$app->add(new WorkerCommand());
$app->add(new CreateServerCommand());
$app->add(new StatusCommand());
$app->run();
