<?php

namespace ChrKo\XmlReaderProxy;

use ChrKo\XmlReaderProxy\Exceptions\AttributeNotFoundExecption;
use ChrKo\XmlReaderProxy\Exceptions\XmlReaderExecption;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class XmlReader extends \XMLReader
{
    public static $guzzleClient;

    public static function getGuzzleClient()
    {
        if (!self::$guzzleClient && !self::$guzzleClient instanceof ClientInterface) {
            self::$guzzleClient = new Client([
                'connect_timeout' => 0.4,
                'timeout' => 1.5,
                'curl' => [
                    CURLOPT_FORBID_REUSE => false,
                ],
                'headers' => [
                    'Connection' => 'Keep-Alive',
                    'Keep-Alive' => '300',
                ]
            ]);
        }

        return self::$guzzleClient;
    }

    public function open($uri, $encoding = null, $options = 0)
    {
        $client = self::getGuzzleClient();

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
