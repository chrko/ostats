#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

include_once __DIR__ . '/config.php';
require_once __DIR__ . '/config.default.php';

use ChrKo\OStats\Command\CreateServerCommand;
use ChrKo\OStats\Command\PartitionCommand;
use ChrKo\OStats\Command\StatusCommand;
use ChrKo\OStats\Command\WorkerCommand;
use Symfony\Component\Console\Application;

$app = new Application();
$app->add(new WorkerCommand());
$app->add(new CreateServerCommand());
$app->add(new StatusCommand());
$app->add(new PartitionCommand());
$app->add(new \ChrKo\OStats\Command\MigrateCommand());
$app->add(new \ChrKo\OStats\Command\GetAllServerIdsCommand());
$app->run();
