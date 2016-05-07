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
        $steps = [];

        define('RESOLUTION_DAY_COUNT', 7);

        $today = new \DateTime('now', new \DateTimeZone('Etc/UTC'));
        $output->writeln('Today: ' . $today->format('c'));

        $date = clone $today;

        $date->setTime(0, 0, 0);
        $date->add(new \DateInterval('P1D'));

        for ($i = 0; $i < RESOLUTION_DAY_COUNT; $i++) {
            $step = [];
            $step['limit_timestamp'] = $date->getTimestamp();
            $step['limit_readable'] = date('c', $step['limit_timestamp']);

            $date->sub(new \DateInterval('P1D'));
            $step['name'] = 'd_' . $date->format('Ymd');

            $steps[] = $step;
        }

        $minDayLimitTimestamp = $step['limit_timestamp'];

        $weekWanted = clone $today;

        $weekWanted->setTime(0, 0, 0);
        $weekWanted->setISODate($weekWanted->format('Y'), (int) $weekWanted->format('W') + 1, 1);

        for ($i = 0; $i < 2; $i++) {
            $step = [];

            $weekWanted->sub(new \DateInterval('P' . RESOLUTION_DAY_COUNT . 'D'));

            $step['limit_timestamp'] = $weekWanted->getTimestamp();

            $step['limit_timestamp'] =
                $step['limit_timestamp'] >= $minDayLimitTimestamp
                    ? $minDayLimitTimestamp - 24 * 60 * 60
                    : $step['limit_timestamp'];

            $step['limit_readable'] = date('c', $step['limit_timestamp']);
            $step['name'] = 'w_' . $weekWanted->format('YW');

            $steps[] = $step;
        }

        //$output->write(print_r($steps, TRUE));

        $steps = array_reverse($steps);

        foreach (XmlApiUpdate::getAllowedArguments()['highscore']['category'] as $category) {
            foreach (XmlApiUpdate::getAllowedArguments()['highscore']['type'] as $type) {
                $table = 'highscore_' . $category . '_' . $type;

                foreach ($steps as $step) {
                    $parts = DB::getConn()
                        ->query('SELECT PARTITION_NAME AS `name`, PARTITION_DESCRIPTION AS `limit` FROM information_schema.PARTITIONS
                    WHERE TABLE_SCHEMA = \'' . DB_NAME . '\'
                    AND TABLE_NAME LIKE \'' . $table . '\'')
                        ->fetch_all(MYSQLI_ASSOC);

                    $step['from'] = [];

                    $pLastSkip = FALSE;
                    foreach ($parts as $part) {
                        if ($part['name'] == 'p0') {
                            continue;
                        }
                        if ($step['name'] == $part['name'] && $step['limit_timestamp'] == (int) $part['limit']) {
                            continue 2;
                        }
                        if ($pLastSkip || $part['limit'] != 'MAXVALUE') {
                            if ($step['limit_timestamp'] > (int) $part['limit']
                                && $step['name'] != $part['name']
                                && !(strpos($step['name'], 'w_') !== FALSE && strpos($part['name'], 'd_') !== FALSE)
                            ) {
                                continue;
                            }
                            if ($step['limit_timestamp'] < (int) $part['limit']) {
                                continue;
                            }
                            if ($step['limit_timestamp'] == (int) $part['limit']) {
                                $pLastSkip = TRUE;
                            }
                        }
                        $step['from'][$part['name']] = [
                            'name' => $part['name'],
                            'limit' => $part['limit'],
                        ];
                        continue;
                    }

                    $sql = "ALTER TABLE ${table} REORGANIZE PARTITION " . join(',', array_keys($step['from']))
                        . " INTO (\n";
                    $sql .= "PARTITION ${step['name']} VALUES LESS THAN (${step['limit_timestamp']})";
                    if (in_array('pLast', array_keys($step['from']))) {
                        $sql .= ",\nPARTITION pLast VALUES LESS THAN MAXVALUE";
                    }

                    $sql .= "\n);";

                    $output->writeln($sql);

                    DB::getConn()->query($sql);
                }
            }
        }
    }
}
