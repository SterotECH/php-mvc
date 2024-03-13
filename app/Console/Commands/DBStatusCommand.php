<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "db:status")]
class DBStatusCommand extends Command
{
    protected static $defaultName = 'db:status';

    protected function configure()
    {
        $this->setDescription('View the status of migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $migrationsFile = base_path('/app/Database/migrations/migrations.php');
        $migrations = include $migrationsFile;

        $tableRows = [];
        foreach ($migrations as $className => $status) {
            $tableRows[] = [$status === 'applied' ? '[✓]' : '[ ]', $className, $status];
        }
        $io->table(['[ ]', 'NAME', 'STATUS'], (array)$tableRows);

        return Command::SUCCESS;
    }
}
