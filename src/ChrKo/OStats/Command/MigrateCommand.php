<?php

namespace ChrKo\OStats\Command;

use ChrKo\OStats\DB;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\ProcessBuilder;

class MigrateCommand extends Command {
    const TRIGGER_DROP_FILE = 'schema_trigger_1_drop.sql';
    const TRIGGER_ADD_FILE = 'schema_trigger_2_add.sql';

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

    public static function getSingleFileByName($name) {
        $finder = new Finder();
        $finder->in(SQL_MIGRATIONS_DIR)->files()->name($name);
        assert($finder->count() == 1);

        return iterator_to_array($finder, false)[0];
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

        if (count($todo) == 0) {
            $output->writeln('<info>Nothing todo.</info>');
            return;
        }

        $output->writeln('<info>There are ' . count($todo) . ' migrations todo.</info>');
        foreach (array_keys($todo) as $index => $key) {
            $output->writeln("<comment>${index}: ${key}</comment>");
        }
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Proceed with the migrations? (y/N) ', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<error>you have aborted applying the migrations</error>');
            return;
        }

        $doneStmt = 'INSERT INTO `schema_history` (migration, deploy_time_int) VALUES (\':migration:\', :deploy_time_int:)';

        $drops = self::getSingleFileByName(self::TRIGGER_DROP_FILE);
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

        $creates = self::getSingleFileByName(self::TRIGGER_ADD_FILE);

        $output->writeln('doing ' . $creates->getFilename());
        self::executeSqlFile($creates);
        $output->writeln('done ' . $creates->getFilename());
    }
}
