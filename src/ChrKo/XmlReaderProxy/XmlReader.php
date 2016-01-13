<?php

namespace ChrKo\XmlReaderProxy;

use ChrKo\XmlReaderProxy\Exceptions\AttributeNotFoundExecption;
use ChrKo\XmlReaderProxy\Exceptions\XmlReaderExecption;

use GuzzleHttp\Client;

class XmlReader extends \XMLReader
{
    protected static $cacheDir = null;

    public static function getCacheDir()
    {
        return self::$cacheDir;
    }

    public static function setCacheDir($cacheDir)
    {
        self::$cacheDir = $cacheDir;
    }

    public function open($uri, $encoding = null, $options = 0)
    {
        $client = new Client([
            'timeout' => 2.0
        ]);

        $response = $client->get($uri);
        if ($response->getStatusCode() != 200) {
            throw new \LogicException('unexpected status code ' . $response->getStatusCode());
        }
        $xml = (string) $response->getBody();

        $return = $this->xml($xml, $encoding, $options);

        if ($return === false) {
            throw new XmlReaderExecption();
        }

        return $return;
    }

    public function read($exception = true)
    {
        $return = parent::read();
        if ($return === false && $exception === true) {
            throw new XmlReaderExecption();
        }
        return $return;
    }

    public function getAttribute($name, $default = null)
    {
        $return = parent::getAttribute($name);

        if ($return === null && $default !== null) {
            return $default;
        }

        if ($return === null && $default === null) {
            throw new AttributeNotFoundExecption($name);
        }

        return $return;
    }
}
