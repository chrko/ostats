<?php

namespace ChrKo\OStats\Command;

use ChrKo\OStats\DB;
use ChrKo\OStats\Task\XmlApiUpdate;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PartitionCommand extends Command {
    protected function configure() {
        $this
            ->setName('gt:part');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!defined('HIGHSCORE_DAY_PAST')) {
            define('HIGHSCORE_DAY_PAST', 7);
        }

        if (!defined('HIGHSCORE_DAY_FUTURE')) {
            define('HIGHSCORE_DAY_FUTURE', 4);
        }

        if (!defined('HIGHSCORE_WEEK_PAST')) {
            define('HIGHSCORE_WEEK_PAST', 2);
        }

        $steps = [];

        $today = new \DateTime('now', new \DateTimeZone('Etc/UTC'));
        $output->writeln('Today: ' . $today->format('c'));
        $today->setTime(24, 0, 0);

        $oneDay = new \DateInterval('P1D');

        for ($i = -HIGHSCORE_DAY_PAST; $i <= HIGHSCORE_DAY_FUTURE; $i++) {
            $date = clone $today;
            if ($i < 0) {
                $date->sub(new \DateInterval('P' . -$i . 'D'));
            }
            else {
                $date->add(new \DateInterval('P' . $i . 'D'));
            }

            $step = [];
            $step['limit_timestamp'] = $date->getTimestamp();
            $date->sub($oneDay);
            $step['name'] = 'd_' . $date->format('Ymd');
            $step['limit_timestamp_low'] = $date->getTimestamp();

            $steps[] = $step;
        }

        $minDayLimitTimestamp = array_reduce(
            $steps,
            function ($carry, $item) {
                if ($carry == NULL) {
                    return $item['limit_timestamp'];
                }
                return min($carry, $item['limit_timestamp']);
            }
        );

        $weekWanted = clone $today;
        $weekWanted->setTimestamp($minDayLimitTimestamp);
        $weekWanted->sub($oneDay);

        for ($i = 0; $i < HIGHSCORE_WEEK_PAST; $i++) {
            $step = [];

            $step['limit_timestamp'] = $weekWanted->getTimestamp();
            $weekWanted->sub($oneDay);
            $step['name'] = 'w_' . $weekWanted->format('YW');
            $weekWanted->setISODate($weekWanted->format('Y'), (int) $weekWanted->format('W'), 1);
            $step['limit_timestamp_low'] = $weekWanted->getTimestamp();

            $steps[] = $step;
        }

        $maxStepTimestamp = array_reduce(
            $steps,
            function ($carry, $item) {
                if ($carry == NULL) {
                    return $item['limit_timestamp'];
                }
                return max($carry, $item['limit_timestamp']);
            }
        );

        $steps[] = [
            'name'                => 'pLast',
            'limit_timestamp'     => $maxSpecialTimestamp = $today->add(new \DateInterval('P1Y'))
                ->getTimestamp(),
            'limit_timestamp_low' => $maxStepTimestamp,
            'max'                 => TRUE
        ];

        array_walk($steps, function (&$el) {
            $el['limit_readable'] = date('c', (int) $el['limit_timestamp']);
            $el['limit_readable_low'] = date('c', (int) $el['limit_timestamp_low']);
            $el['max'] = $el['max'] ?? FALSE;
        });

        usort($steps, function ($a, $b) {
            return $a['limit_timestamp'] <=> $b['limit_timestamp'];
        });

        $origSteps = $steps;

        foreach (XmlApiUpdate::getAllowedArguments()['highscore']['category'] as $category) {
            foreach (XmlApiUpdate::getAllowedArguments()['highscore']['type'] as $type) {
                $table = 'highscore_' . $category . '_' . $type;
                $steps = $origSteps;

                $parts = DB::getConn()
                    ->query('SELECT PARTITION_NAME AS `name`, PARTITION_DESCRIPTION AS `limit_timestamp` FROM information_schema.PARTITIONS
                    WHERE TABLE_SCHEMA = \'' . DB_NAME . '\'
                    AND TABLE_NAME LIKE \'' . $table . '\'')
                    ->fetch_all(MYSQLI_ASSOC);

                array_walk($parts, function (&$el) use ($maxSpecialTimestamp) {
                    if ($el['limit_timestamp'] == 'MAXVALUE') {
                        $el['limit_timestamp'] = $maxSpecialTimestamp;
                        $el['max'] = TRUE;
                    }
                    $el['limit_timestamp'] = (int) $el['limit_timestamp'];
                    $el['limit_readable'] = date('c', (int) $el['limit_timestamp']);
                    $el['max'] = $el['max'] ?? FALSE;
                });

                usort($parts, function ($a, $b) {
                    return $a['limit_timestamp'] <=> $b['limit_timestamp'];
                });

                $unitsOfWork = [];

                foreach ($parts as $pKey => $part) {
                    if (count($unitsOfWork) > 0
                        && $unitsOfWork[$usOWKey = count($unitsOfWork) - 1]['to_max_limit'] >= $part['limit_timestamp']
                    ) {
                        $unitsOfWork[$usOWKey]['from'][] = $part;
                        continue;
                    }
                    $unitOfWork = ['from' => [$part], 'to' => []];
                    foreach ($steps as $sKey => $step) {
                        if (!$part['max']
                            && (
                                $part['limit_timestamp'] <= $step['limit_timestamp_low']
                                || $part['limit_timestamp'] > $step['limit_timestamp']
                            )
                        ) {
                            continue;
                        }

                        if (!($part['name'] == $step['name'] && $part['limit_timestamp'] == $step['limit_timestamp'] && !($part['max'] && count($unitOfWork['to']) > 0))) {
                            $unitOfWork['to'][] = $step;
                        }

                        unset($steps[$sKey]);
                        continue;
                    }
                    if (count($unitOfWork['to']) > 0) {
                        $unitOfWork['to_max_limit'] = array_reduce(
                            $unitOfWork['to'],
                            function ($carry, $item) {
                                if ($carry == NULL) {
                                    return $item['limit_timestamp'];
                                }
                                return max($carry, $item['limit_timestamp']);
                            }
                        );
                        $unitsOfWork[] = $unitOfWork;
                    }
                }

                foreach ($unitsOfWork as $unitOfWork) {
                    $fromCount = count($unitOfWork['from']);
                    $toCount = count($unitOfWork['to']);

                    $nameContains = function ($array, $needle) {
                        return array_reduce(
                            $array,
                            function ($carry, $item) use ($needle) {
                                return $carry || strpos($item['name'], $needle) !== FALSE;
                            },
                            FALSE);
                    };

                    if ($nameContains($unitOfWork['from'], 'w_') && $nameContains($unitOfWork['to'], 'd_')) {
                        $output->writeln('<error>Traveling backwards is not supported</error>');
                        break;
                    }
                    if ($nameContains($unitOfWork['from'], 'd_') && $nameContains($unitOfWork['to'], 'w_')) {
                        $dayPartsToReduce = array_filter($unitOfWork['from'], function ($val) {
                            return strpos($val['name'], 'd_') !== FALSE;
                        });
                        foreach ($dayPartsToReduce as $dayPart) {
                            $lowerBoundary = \DateTime::createFromFormat('*Ymd', $dayPart['name'], new \DateTimeZone('Etc/UTC'))
                                ->setTime(0, 0, 0)
                                ->setTimezone(new \DateTimeZone('Europe/Berlin'))
                                ->setTime(3, 0, 0);
                            $upperBoundary = (clone $lowerBoundary);
                            $upperBoundary->setTime(4, 15, 0);

                            $lowerBoundaryTimestamp = $lowerBoundary->getTimestamp();
                            $upperBoundaryTimestamp = $upperBoundary->getTimestamp();
                            $sql = "DELETE FROM `${table}` PARTITION (`${dayPart['name']}`) WHERE `seen_int` >= ${lowerBoundaryTimestamp} AND `seen_int` < ${upperBoundaryTimestamp};\n";

                            $output->write(print_r($sql, TRUE));

                            DB::getConn()->query($sql);
                        }
                    }

                    $fromPartsStr = substr(array_reduce(
                        $unitOfWork['from'],
                        function ($carry, $item) {
                            return $carry . '`' . $item['name'] . '`, ';
                        },
                        ''
                    ), 0, -2);
                    $sql = "ALTER TABLE `${table}` REORGANIZE PARTITION ${fromPartsStr} INTO (\n";

                    foreach ($unitOfWork['to'] as $step) {
                        if (!$step['max']) {
                            $sql .= "PARTITION `${step['name']}` VALUES LESS THAN (${step['limit_timestamp']}),\n";
                        }
                        else {
                            $sql .= "PARTITION `${step['name']}` VALUES LESS THAN MAXVALUE,\n";
                        }
                    }
                    $sql = substr($sql, 0, -2);

                    $sql .= ');' . "\n";

                    $output->write(print_r($sql, TRUE));

                    DB::getConn()->query($sql);
                }
            }
        }
    }
}
