<?php

namespace ChrKo\XmlReaderProxy;

use ChrKo\XmlReaderProxy\Exceptions\AttributeNotFoundExecption;
use ChrKo\XmlReaderProxy\Exceptions\XmlReaderExecption;

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
        if(self::$cacheDir === null) {
            $return = parent::open($uri, $encoding, $options);
        } else {
            $xml = file_get_contents($uri);
            $return = $this->xml($xml, $encoding, $options);
        }

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
