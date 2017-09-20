<?php

namespace ChrKo\OStats\Command;

use ChrKo\OStats\BulkQuery\ScheduleInsert;
use ChrKo\OStats\DB;
use ChrKo\OStats\OGame\API\XML;
use ChrKo\OStats\Task\Scheduler;
use ChrKo\OStats\Task\XmlApiUpdate;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateServerCommand extends Command {
    protected function configure() {
        $this
            ->setName('gt:server:create')
            ->addArgument(
                'server_id',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED
            )
            ->addOption(
                'full-country',
                'f',
                InputOption::VALUE_NONE
            )
            ->addOption(
                'force-queue',
                null,
                InputOption::VALUE_NONE
            )
            ->addOption(
                'endpoint',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL
            )
            ->addOption(
                'category',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL
            )
            ->addOption(
                'type',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL
            )
            ->addOption(
                'schedule-delay',
                'd',
                InputOption::VALUE_OPTIONAL,
                '',
                0
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $serverIds = $input->getArgument('server_id');

        if ($input->getOption('full-country')) {
            $tmpServerIds = $serverIds;
            foreach ($tmpServerIds as $serverId) {
                $serverIds = array_merge($serverIds, XML\Universes::readData($serverId)['server_ids']);
            }
        }

        $bulkScheduler = new ScheduleInsert(DB::getConn());
        if ($input->getOption('force-queue')) {
            $bulkScheduler->forceReschedule = true;
        }

        $serverIds = array_unique($serverIds);

        $count = 0;
        $interval = $input->getOption('schedule-delay');
        $start = time();

        foreach ($serverIds as $serverId) {
            foreach (XML::getAllowedArguments() as $endpoint => $details) {
                if (count($input->getOption('endpoint')) > 0 && !in_array($endpoint, $input->getOption('endpoint'))) {
                    continue;
                }

                $categories = $details['category'];
                $types = $details['type'];
                foreach ($categories as $category) {
                    if (count($input->getOption('category')) > 0 && !in_array($category, $input->getOption('category'))) {
                        continue;
                    }
                    foreach ($types as $type) {
                        if (count($input->getOption('type')) > 0 && !in_array($type, $input->getOption('type'))) {
                            continue;
                        }
                        $bulkScheduler->run(Scheduler::prepare(
                            new XmlApiUpdate($serverId, $endpoint, $category, $type, $start + $count * $interval)
                        ));
                        $count++;
                    }
                }
            }
        }

        $bulkScheduler->finish();
    }
}
