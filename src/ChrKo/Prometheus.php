<?php

namespace ChrKo;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;
use Symfony\Component\Stopwatch\Stopwatch;

class Prometheus
{
    /**
     * @var CollectorRegistry
     */
    private static $registry;

    /**
     * @var Stopwatch
     */
    private static $stopwatch;

    /**
     * @var int
     */
    private static $processId;

    private function __construct()
    {
    }

    public static function getRegistry(): CollectorRegistry
    {
        if (!self::$registry) {
            return self::$registry = new CollectorRegistry(new Redis(
                [
                    'persistent_connections' => true
                ]
            ));
        }
        return self::$registry;
    }

    /**
     * @return Stopwatch
     */
    public static function getStopwatch(): Stopwatch
    {
        if (!self::$stopwatch) {
            return self::$stopwatch = new Stopwatch(true);
        }
        return self::$stopwatch;
    }

    /**
     * @return int
     */
    public static function getProcessId(): int
    {
        if (!is_int(self::$processId)) {
            return self::$processId = getmypid();
        }
        return self::$processId;
    }
}
