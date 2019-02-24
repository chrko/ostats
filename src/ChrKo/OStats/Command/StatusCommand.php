<?php

namespace ChrKo\OStats\Command;

use ChrKo\OStats\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command {
    protected function configure() {
        $this
            ->setName('gt:status');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $where = ' 1 ';
        if (DISABLE_PLAYER) {
            $where = ' `job_type` != \'xml-player\' ';
        }
        $dbResults = [];
        $dbResults['totalTasks'] = DB::getConn()->query(
            'SELECT COUNT(*) AS `count`, `job_type` FROM `tasks` WHERE ' . $where . ' GROUP BY `job_type`');
        $dbResults['delayedTasks'] = DB::getConn()->query(
            'SELECT COUNT(*) AS `count`, `job_type` FROM `tasks`'
            . ' WHERE `running` = 0 AND `due_time_int` < ' . time() . ' AND ' . $where
            . ' GROUP BY `job_type`');
        $dbResults['delayedTime'] = DB::getConn()->query(
            'SELECT MIN(`due_time_int`) AS `min_due_time`, `job_type` FROM `tasks`'
            . ' WHERE `running` = 0 AND `due_time_int` < ' . time() . ' AND ' . $where
            . ' GROUP BY `job_type`'
        );

        $totalTasks = 0;
        $totalTasksPerJobType = [];

        $delayedTasks = 0;
        $delayedTasksPerJobType = [];

        while ($row = $dbResults['totalTasks']->fetch_assoc()) {
            $totalTasks += (int) $row['count'];
            $totalTasksPerJobType[$row['job_type']] = (int) $row['count'];
        }

        while ($row = $dbResults['delayedTasks']->fetch_assoc()) {
            $delayedTasks += (int) $row['count'];
            $delayedTasksPerJobType[$row['job_type']] = (int) $row['count'];
        }

        $delayTime = time();
        $delayTimePerJobType = [];
        while ($row = $dbResults['delayedTime']->fetch_assoc()) {
            $timestamp = (int) $row['min_due_time'];
            $delayTime = $delayTime < $timestamp ? $delayTime : $timestamp;
            $delayTimePerJobType[$row['job_type']] = $timestamp;
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
            foreach ($totalTasksPerJobType as $jobType => $totalTasks) {
                $output->writeln('JobType <info>' . $jobType . '</info>:');

                $output->write([
                    'Totals tasks: ',
                    $totalTasks
                ]);
                $output->writeln('');

                if (array_key_exists($jobType, $delayedTasksPerJobType)) {
                    $output->write([
                        'Delayed tasks: ',
                        $delayedTasksPerJobType[$jobType],
                        ' | ',
                        number_format($delayedTasksPerJobType[$jobType] / $totalTasks * 100, 2),
                        '%'
                    ]);
                    $output->writeln('');

                    $output->write([
                        'Delay time: ',
                        date('H:i:s', time() - $delayTimePerJobType[$jobType])
                    ]);
                    $output->writeln('');
                }

                $output->writeln('');
            }
        }
    }
}
