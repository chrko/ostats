<?php

namespace ChrKo\OStats\Command;

use ChrKo\OStats\DB;
use ChrKo\OStats\Task\Executor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WorkerCommand extends Command {
    protected function configure() {
        $this
            ->setName('gt:worker')
            ->setDescription('Executing worker instance')
            ->addArgument(
                'endpoints',
                InputArgument::IS_ARRAY,
                'Filter tasks by these specified endpoints'
            )
            ->addOption(
                'allow-restriction-ignorance',
                'f',
                InputOption::VALUE_NONE,
                'allow restriction ignorance filters'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $whereRestriction = '';

        $endpoints = $input->getArgument('endpoints');

        if (is_array($endpoints) && count($endpoints) > 0) {
            $whereRestriction .= ' AND (';
            $tmp = '';
            foreach ($endpoints as $endpoint) {
                $endpoint = DB::getConn()->real_escape_string($endpoint);
                $tmp .= " OR `job_type` = 'xml-${endpoint}'";
            }
            $whereRestriction .= substr($tmp, 4);
            $whereRestriction .= ') ';
            unset($tmp);
        }

        $executor = new Executor($whereRestriction, !$input->getOption('allow-restriction-ignorance'));

        declare(ticks = 1);
        pcntl_signal(SIGTERM, [$executor, 'stop']);
        pcntl_signal(SIGINT, [$executor, 'stop']);

        $executor->work();
    }
}
