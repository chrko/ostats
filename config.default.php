<?php

date_default_timezone_set('UTC');

//define('DB_HOST', 'localhost');
//define('DB_USER', 'user');
//define('DB_PASS', 'pass');
//define('DB_NAME', 'o_collector');

!defined('MIN_SLEEP_TIME') ? define('MIN_SLEEP_TIME', 10) : false;
!defined('MAX_SLEEP_TIME') ? define('MAX_SLEEP_TIME', 60) : false;

!defined('DISABLE_PLAYER') ? define('DISABLE_PLAYER', false) : false;

!defined('SQL_MIGRATIONS_DIR') ? define('SQL_MIGRATIONS_DIR', __DIR__ . '/sql') : false;