<?php

namespace ChrKo\OStats\BulkQuery;


interface ExecutorInterface
{
    public function run($data);
    public function finish();
    public function clean($server_id, $last_update);
}
