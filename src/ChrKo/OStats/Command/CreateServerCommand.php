<?php

namespace ChrKo\OStats\Command;

use ChrKo\OStats\Task\XmlApiUpdate;
use ChrKo\OStats\XmlApi;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateServerCommand extends Command
{
    protected function configure()
    {
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serverIds = $input->getArgument('server_id');

        if ($input->getOption('full-country')) {
            $tmpServerIds = $serverIds;
            foreach ($tmpServerIds as $serverId) {
                $serverIds = array_merge($serverIds, XmlApi::readLocalServers($serverId));
            }
        }

        $count = 0;
        $interval = 0;
        $start = time();

        foreach ($serverIds as $serverId) {
            foreach (XmlApiUpdate::getAllowedArguments() as $endpoint => $details) {
                $categories = $details['category'];
                $types = $details['type'];
                foreach ($categories as $category) {
                    foreach ($types as $type) {
                        (new XmlApiUpdate($serverId, $endpoint, $category, $type, $start + $count * $interval))->save();
                        $count++;
                    }
                }
            }
        }
    }
}
