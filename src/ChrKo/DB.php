<?php

namespace ChrKo;


class DB extends \mysqli
{
    /**
     * @var bool|DB
     */
    protected static $dbConn = false;

    public static function namedReplace($query, $data)
    {
        $search = array_map(
            function ($v) {
                return ':' . $v;
            },
            array_keys($data)
        );
        $replace = array_values($data);

        return str_replace($search, $replace, $query);
    }

    public static function getConn()
    {
        if (!self::$dbConn || !self::$dbConn->ping()) {
            self::$dbConn = new self(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        }

        return self::$dbConn;
    }

    public static function formatTimestamp($timestamp = null)
    {
        $timestamp = $timestamp ?: time();
        return date('Y-m-d H:i:s', $timestamp);
    }

    public function query($query, $resultmode = MYSQLI_STORE_RESULT)
    {
        $result = parent::query($query, $resultmode);

        if ($this->errno != 0) {
            var_dump($query);
            throw new \Exception($this->error);
        }

        return $result;
    }
}
