<?php

namespace ChrKo\OStats\OGame\API\XML;

use ChrKo\OStats\OGame\API\XML;
use ChrKo\XmlReaderProxy\XmlReader;

class ServerData {
    const UPDATE_INTERVAL = 2 * 60 * 60;

    public static function readData($serverId) {
        $xmlReader = new XmlReader();
        $xmlReader->open(XML::getServerBaseById($serverId) . 'api/serverData.xml');

        $xmlReader->read();
        if ($xmlReader->name != 'serverData') {
            throw new UnknownElementException('serverData', $xmlReader->name);
        }

        $lastUpdateInt = (int) $xmlReader->getAttribute('timestamp');
        $lastUpdate = date('Y-m-d H:i:s', $lastUpdateInt);

        $serverData = [];

        $string = function ($v) {
            return (string) $v;
        };
        $int = function ($v) {
            return (int) $v;
        };
        $double = function ($v) {
            return (double) $v;
        };
        $bool = function ($v) {
            return (bool) (int) $v;
        };
        $timezoneOffset = function ($v) {
            list($hours, $minutes) = explode(':', substr($v, 1));
            $signum = substr($v, 0, 1);
            $hours = (int) $hours;
            $minutes = (int) $minutes;

            $result = ($hours * 60 + $minutes) * ($signum == '-' ? -1 : 1);

            return $result;
        };

        $elements = [
            'name'                          => $string,
            'number'                        => $int,
            'language'                      => $string,
            'timezone'                      => $string,
            'timezoneOffset'                => $timezoneOffset,
            'domain'                        => $string,
            'version'                       => $string,
            'speed'                         => $int,
            'speedFleet'                    => $int,
            'galaxies'                      => $int,
            'systems'                       => $int,
            'acs'                           => $bool,
            'rapidFire'                     => $bool,
            'defToTF'                       => $bool,
            'debrisFactor'                  => $double,
            'debrisFactorDef'               => $double,
            'repairFactor'                  => $double,
            'newbieProtectionLimit'         => $int,
            'newbieProtectionHigh'          => $int,
            'topScore'                      => $int,
            'bonusFields'                   => $int,
            'donutGalaxy'                   => $bool,
            'donutSystem'                   => $bool,
            'wfEnabled'                     => $bool,
            'wfMinimumRessLost'             => $int,
            'wfMinimumLossPercentage'       => $int,
            'wfBasicPercentageRepairable'   => $int,
            'globalDeuteriumSaveFactor'     => $double,
            'bashlimit'                     => $int,
            'probeCargo'                    => $int,
            'researchDurationDivisor'       => $int,
            'darkMatterNewAcount'           => $int,
            'cargoHyperspaceTechMultiplier' => $int,
        ];

        while ($xmlReader->read(false)) {
            if ($xmlReader->nodeType !== XmlReader::ELEMENT) {
                continue;
            }

            if (array_key_exists($xmlReader->name, $elements)) {
                $serverData[$xmlReader->name] = $elements[$xmlReader->name]($xmlReader->readString());
            } else {
                var_dump([$xmlReader->name, $xmlReader->readString()]);
            }
        }

        return [
            'server_id'       => $serverId,
            'last_update'     => $lastUpdate,
            'last_update_int' => $lastUpdateInt,
            'server_data'     => $serverData,
        ];
    }
}
