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

        if(defined('CACHE_DIR')) {
            $tmp = new self();
            $tmp->xml($xml, $encoding, $options);
            $tmp->read();
            $timestamp = $tmp->getAttribute('timestamp');
            $tmp->close();
            unset($tmp);
            $uri_parts = parse_url($uri);
            if (preg_match('/^\/(.*)\/(.*).xml/', $uri_parts['path'], $matches) !== 1) {

            }
            $query_parts = [];
            $query_raw_parts = isset($uri_parts['query']) ? explode('&', $uri_parts['query']) : [];
            foreach ($query_raw_parts as $part) {
                if (strlen($part) == 0) {
                    continue;
                }
                $part = explode('=', $part);
                $query_parts[$part[0]] = $part[1];
            }
            ksort($query_parts);
            $query_part = '';
            foreach ($query_parts as $key => $value) {
                $query_part .= $key . '=' . $value . '_';
            }
            $filename = CACHE_DIR . DIRECTORY_SEPARATOR . $uri_parts['host'] . DIRECTORY_SEPARATOR . $matches[2] . '_' . $query_part . $timestamp . '.xml';
            @mkdir(dirname($filename), 0777, true);
            if (is_dir(dirname($filename))) {
                file_put_contents($filename, $xml);
            }
        }

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
