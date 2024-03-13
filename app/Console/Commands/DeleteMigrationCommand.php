<?php

namespace App\Console\Commands;

use App\Core\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "delete:migration")]
class DeleteMigrationCommand extends Command
{
    protected static $defaultName = 'delete:migration';

    protected function configure()
    {
        $this->setDescription('Delete a migration file and its corresponding database record.');
        $this->addArgument('migration', InputArgument::IS_ARRAY, 'The name of the migration file to delete (space separated).')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the operation to run when in production.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $migrationsFile = base_path('/app/Database/migrations/migrations.php');
        $migrations = include $migrationsFile;
        $migrationsDir = base_path('/app/Database/migrations');

        $selectedMigrations = $input->getArgument('migration');

        if (empty($selectedMigrations)) {
            $io->info('No migration files specified.');

            $choices = [];
            foreach ($migrations as $migrationNumber => $status) {
                $choices[] = "$migrationNumber";
            }

            $selectedMigrations = $io->choice('Select the migrations to delete', $choices, multiSelect: true);
        }

        if (!$input->getOption('force')) {
            $io->error('This action is dangerous and can delete important data. Use --force to confirm.');
            return Command::FAILURE;
        }

        foreach ($selectedMigrations as $selectedMigration) {
            if (isset($migrations[$selectedMigration])) {
                $migrationName = $migrations[$selectedMigration];

                if ($migrationName === 'applied') {
                    $database = Database::getInstance();

                    $migrationFile = $migrationsDir . '/' . $selectedMigration . '.php';
                    include_once $migrationFile;

                    $className = basename($migrationFile, '.php');
                    $migration = new $className();

                    $migration->down();

                    $database->query("DELETE FROM migrations WHERE migration = '$selectedMigration'");

                    $migrations[$selectedMigration] = 'unapplied';

                    $io->success("Undone and deleted migration: $selectedMigration");
                }

                $migrationFile = $migrationsDir . '/' . $selectedMigration . '.php';
                unlink($migrationFile);

                unset($migrations[$selectedMigration]);

                $io->success("Deleted migration: $selectedMigration");
            } else {
                $io->error("Migration not found: $selectedMigration");
            }
        }

        file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';');

        return Command::SUCCESS;
    }
}
