<?php

namespace ChrKo\OStats\Command;

use ChrKo\OStats\OGame\API\XML;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetAllServerIdsCommand extends Command {
    protected function configure() {
        $this
            ->setName('gt:server:all-ids')
            ->addArgument(
                'server_id',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $tmpServerIds = $input->getArgument('server_id');

        $serverIds = array();
        foreach ($tmpServerIds as $serverId) {
            $serverIds = array_merge($serverIds, XML\Universes::readData($serverId)['server_ids']);
        }

        $serverIds = array_unique($serverIds);
        sort($serverIds);
        foreach ($serverIds as $serverId) {
            $output->writeln($serverId);
        }
    }
}
