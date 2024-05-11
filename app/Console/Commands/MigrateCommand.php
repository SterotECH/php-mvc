<?php

namespace App\Console\Commands;

use App\Core\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'migrate')]
class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';

    protected function configure()
    {
        $this->setDescription('Run database migrations')
            ->addArgument('migration', InputArgument::OPTIONAL, 'Migration file to run')
            ->setHelp('This command allows you to run database migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Running migrations');

        $migrationsFile = base_path('/app/Database/migrations/migrations.php');
        $migrations = include $migrationsFile;
        $migrationsDir = base_path('/app/Database/migrations');

        $database = Database::getInstance();
        $id = env('DB_CONNECTION') === 'pgsql' ? 'id SERIAL PRIMARY KEY' : 'id INT AUTO_INCREMENT PRIMARY KEY';
        $database->query("
            CREATE TABLE IF NOT EXISTS migrations (
                $id,
                migration VARCHAR(255) NOT NULL,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

        $argument = $input->getArgument('migration');

        if ($argument) {
            if (array_key_exists($argument, $migrations) && $migrations[$argument] === 'unapplied') {
                include $migrationsDir . '/' . $argument . '.php';
                $migration = new $argument();
                $database->query($migration->up());
                $database->query("INSERT INTO migrations (migration) VALUES ('$argument')");
                $migrations[$argument] = 'applied';
                file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';' . PHP_EOL);
                $io->text("Applied migration: $argument");
            } else {
                $io->text("Migration not found or already applied: $argument");
            }
        } else {
            foreach ($migrations as $className => $status) {
                if ($status === 'unapplied') {
                    include $migrationsDir . '/' . $className . '.php';
                    $migration = new $className();
                    $database->query($migration->up());
                    $database->query("INSERT INTO migrations (migration) VALUES ('$className')");
                    $migrations[$className] = 'applied';
                    file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';');
                    $io->text("Applied migration: $className");
                } else {
                    $io->text("Migration already applied: $className");
                }
            }
        }

        $io->success('Database migrated!');

        return Command::SUCCESS;
    }
}
