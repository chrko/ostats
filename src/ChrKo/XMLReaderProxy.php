<?php

namespace ChrKo;

class XMLReaderProxy extends \XMLReader
{
    public function open($URI, $encoding = null, $options = 0)
    {
        $return = parent::open($URI, $encoding, $options);

        if ($return === false) {
            throw new \Exception;
        }

        return $return;
    }

    public function read($exception = false)
    {
        $return = parent::read();
        if ($return === false && $exception === true) {
            throw new \Exception;
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
            throw new \Exception("Attribute ${name} not found.");
        }

        return $return;
    }
}