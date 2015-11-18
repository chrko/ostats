<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 17.11.15
 * Time: 18:12
 */

namespace ChrKo;


interface UpdaterInterface
{
    public function run($data);
    public function finish();
    public static function clean($server_id, $last_update);
}