<?php

namespace ChrKo\OStats\Command;

use ChrKo\OStats\DB;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('gt:status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $where = ' 1 ';
        if (DISABLE_PLAYER) {
            $where = ' `endpoint` != \'player\' ';
        }
        $dbResults = [];
        $dbResults['totalTasks'] = DB::getConn()->query(
            'SELECT COUNT(*) AS `count`, `endpoint` FROM `tasks` WHERE ' . $where . ' GROUP BY `endpoint`');
        $dbResults['delayedTasks'] = DB::getConn()->query(
            'SELECT COUNT(*) AS `count`, `endpoint` FROM `tasks`'
            . ' WHERE `running` = 0 AND `due_time` < \'' . \ChrKo\OStats\DB::formatTimestamp() . '\' AND ' . $where
            . ' GROUP BY `endpoint`');
        $dbResults['delayedTime'] = DB::getConn()->query(
            'SELECT MIN(`due_time`) AS `min_due_time`, `endpoint` FROM `tasks`'
            . ' WHERE `running` = 0 AND `due_time` < \'' . \ChrKo\OStats\DB::formatTimestamp() . '\' AND ' . $where
            . ' GROUP BY `endpoint`'
        );

        $totalTasks = 0;
        $totalTasksPerEndpoint = [];

        $delayedTasks = 0;
        $delayedTasksPerEndpoint = [];

        while ($row = $dbResults['totalTasks']->fetch_assoc()) {
            $totalTasks += (int) $row['count'];
            $totalTasksPerEndpoint[$row['endpoint']] = (int) $row['count'];
        }

        while ($row = $dbResults['delayedTasks']->fetch_assoc()) {
            $delayedTasks += (int) $row['count'];
            $delayedTasksPerEndpoint[$row['endpoint']] = (int) $row['count'];
        }

        $delayTime = time();
        $delayTimePerEndpoint = [];
        while ($row = $dbResults['delayedTime']->fetch_assoc()) {
            $timestamp = strtotime($row['min_due_time']);
            $delayTime = $delayTime < $timestamp ? $delayTime : $timestamp;
            $delayTimePerEndpoint[$row['endpoint']] = $timestamp;
        }

        $output->write([
            'Total tasks: ',
            $totalTasks
        ]);
        $output->writeln('');

        $output->write([
            'Delayed tasks: ',
            $delayedTasks,
            ' | ',
            number_format($delayedTasks / $totalTasks * 100, 2),
            '%'
        ]);
        $output->writeln('');

        $output->write([
            'Delay time: ',
            date('H:i:s', time() - $delayTime)
        ]);
        $output->writeln('');
        $output->writeln('');

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            foreach ($totalTasksPerEndpoint as $endpoint => $totalTasks) {
                $output->writeln('Endpoint <info>' . $endpoint . '</info>:');

                $output->write([
                    'Totals tasks: ',
                    $totalTasks
                ]);
                $output->writeln('');

                if (array_key_exists($endpoint, $delayedTasksPerEndpoint)) {
                    $output->write([
                        'Delayed tasks: ',
                        $delayedTasksPerEndpoint[$endpoint],
                        ' | ',
                        number_format($delayedTasksPerEndpoint[$endpoint] / $totalTasks * 100, 2),
                        '%'
                    ]);
                    $output->writeln('');

                    $output->write([
                        'Delay time: ',
                        date('H:i:s', time() - $delayTimePerEndpoint[$endpoint])
                    ]);
                    $output->writeln('');
                }

                $output->writeln('');
            }
        }
    }
}
