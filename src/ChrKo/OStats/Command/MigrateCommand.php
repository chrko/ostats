<?php

namespace ChrKo\OStats\Command;

use ChrKo\OStats\DB;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\ProcessBuilder;

class MigrateCommand extends Command {
    protected function configure() {
        $this
            ->setName('gt:migrate');
    }

    private static function executeSqlFile(SplFileInfo $sqlFile) {
        $processTemplate = new ProcessBuilder(
            [
                'mysql',
                '--database=' . DB_NAME,
                '--host=' . DB_HOST,
                '--password=' . DB_PASS,
                '--user=' . DB_USER,
            ]
        );

        $process = $processTemplate->getProcess();
        $process->setInput($sqlFile->getContents());
        $process->mustRun();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $finder = new Finder();

        $finder->in(SQL_MIGRATIONS_DIR)->files()->name('*.sql')->notName('schema_trigger*');

        $files = [];
        foreach ($finder as $file) {
            $files[$file->getFilename()] = $file;
        }

        $result = DB::getConn()->query('SELECT `migration` FROM `schema_history`;');
        $rows = $result->fetch_all();
        $previouslyDone = array_map(function ($v) {
            return $v[0];
        }, $rows);

        $todo = array_diff_key($files, array_flip($previouslyDone), ['schema_trigger.sql' => 0]);
        ksort($todo);

        $doneStmt = 'INSERT INTO `schema_history` (migration, deploy_time_int) VALUES (\':migration:\', :deploy_time_int:)';

        $finder = new Finder();
        $finder->in(SQL_MIGRATIONS_DIR)->files()->name('schema_trigger*.sql');
        if ($finder->count() != 2) {
            throw new \Exception();
        }

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file;
        }
        usort($files, function (SplFileInfo $a, SplFileInfo $b) {
            return -1 * strcmp($a->getFilename(), $b->getFilename());
        });

        $drops = array_pop($files);


        $output->writeln('doing ' . $drops->getFilename());
        self::executeSqlFile($drops);
        $output->writeln('done ' . $drops->getFilename());


        foreach ($todo as $migration) {
            /** @var SplFileInfo $migration */

            $output->writeln('doing ' . $migration->getFilename());
            self::executeSqlFile($migration);
            $output->writeln('done ' . $migration->getFilename());

            $output->writeln('logging ' . $migration->getFilename());
            DB::getConn()->query(DB::namedReplace($doneStmt, [
                'migration'       => $migration->getFilename(),
                'deploy_time_int' => time(),
            ]));
            $output->writeln('logged ' . $migration->getFilename());
        }

        $creates = array_pop($files);

        $output->writeln('doing ' . $creates->getFilename());
        self::executeSqlFile($creates);
        $output->writeln('done ' . $creates->getFilename());
    }
}
