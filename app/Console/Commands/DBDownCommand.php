<?php

namespace App\Console\Commands;

use App\Core\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: "db:down")]
class DBDownCommand extends Command
{
    protected static $defaultName = 'db:down';

    protected function configure()
    {
        $this->setDescription('Reverts migrations')
            ->setHelp('Reverts migrations')
            ->addArgument('migrations', InputArgument::IS_ARRAY, 'Space-separated list of migrations to run down')
            ->addOption('all', 'a', null, 'Run down all migrations')
            ->addOption('force', 'f', null, 'Force run down (delete database and mark all migrations as unapplied)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $migrationsFile = base_path('/app/Database/migrations/migrations.php');
        $migrations = include $migrationsFile;
        $migrationsDir = base_path('/app/Database/migrations');

        $database = Database::getInstance();

        if ($input->getOption('force')) {
            $io->caution('This action will delete the database and mark all migrations as unapplied.');
            if (!$io->confirm('Do you want to continue?', false)) {
                return Command::SUCCESS;
            }

            $database->drop();
            $database->create();

            foreach ($migrations as $migration => $status) {
                $migrations[$migration] = 'unapplied';
            }
            file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';' . PHP_EOL);
            $io->success('Database deleted and all migrations marked as unapplied.');
        } else {
            $migrationsToRunDown = $input->getArgument('migrations');

            if ($input->getOption('all')) {
                $migrationsToRunDown = array_keys(array_filter($migrations, fn ($status) => $status === 'applied'));
            } elseif (empty($migrationsToRunDown)) {
                $io->warning('No migrations specified. Use --all option to run down all migrations.');
                return Command::FAILURE;
            }

            foreach ($migrationsToRunDown as $className => $status) {
                if (isset($migrations[$className]) && $status === 'applied') {
                    include $migrationsDir . '/' . $className . '.php';
                    $migration = new $className();
                    $database->query($migration->down());
                    $database->query("DELETE FROM migrations WHERE migration = '$className'");
                    $migrations[$className] = 'unapplied';
                    file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';' . PHP_EOL);
                    $io->text("Reverted migration: $className");
                } else {
                    $io->text("Migration not found or not applied: $className");
                }
            }
            $io->success('Migrations reverted successfully');
        }

        return Command::SUCCESS;
    }
}
