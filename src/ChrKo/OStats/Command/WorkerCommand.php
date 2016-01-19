<?php

namespace ChrKo\OStats\Command;

use ChrKo\OStats\DB;
use ChrKo\OStats\Task\Executor;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WorkerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('gt:worker')
            ->setDescription('Executing worker instance')
            ->addArgument(
                'endpoints',
                InputArgument::IS_ARRAY,
                'Filter tasks by these specified endpoints'
            )
            ->addOption(
                'category',
                null,
                InputOption::VALUE_OPTIONAL,
                'Filter tasks by this category'
            )
            ->addOption(
                'type',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Filter tasks by these types'
            )
            ->addOption(
                'allow-restriction-ignorance',
                'f',
                InputOption::VALUE_NONE,
                'Enforce filters'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $whereRestriction = '';

        $endpoints = $input->getArgument('endpoints');
        $category = $input->getOption('category');
        $types = $input->getOption('type');

        if (is_array($endpoints) && count($endpoints) > 0) {
            $whereRestriction .= ' AND (';
            $tmp = '';
            foreach ($endpoints as $endpoint) {
                $endpoint = DB::getConn()->real_escape_string($endpoint);
                $tmp .= " OR `endpoint` = '${endpoint}'";
            }
            $whereRestriction .= substr($tmp, 4);
            $whereRestriction .= ') ';
            unset($tmp);
        }

        if ($category !== null) {
            $category = DB::getConn()->real_escape_string($category);
            $whereRestriction .= " AND `category` = '${category}' ";
        }

        if (is_array($types) && count($types) > 0) {
            $whereRestriction .= ' AND (';
            $tmp = '';
            foreach ($types as $type) {
                $type = DB::getConn()->real_escape_string($type);
                $tmp .= " OR `type` = '${type}'";
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
