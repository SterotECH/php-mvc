<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'db:refresh')]
class DBRefreshCommand extends Command
{
    protected static $defaultName = 'db:refresh';

    protected function configure()
    {
        $this->setDescription('Refreshes the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Refreshing database');

        $downCommand = $this->getApplication()->find('db:down');
        $downArguments = ['command' => 'db:down', '--force' => true];
        $downInput = new ArrayInput($downArguments);
        $downCommand->run($downInput, $output);

        // Run migrate command
        $migrateCommand = $this->getApplication()->find('migrate');
        $migrateArguments = [];
        $migrateInput = new ArrayInput($migrateArguments);
        $migrateCommand->run($migrateInput, $output);

        $io->success('Database refreshed');
        return Command::SUCCESS;
    }
}
