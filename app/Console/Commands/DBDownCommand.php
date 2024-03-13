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
            ->addArgument('migrations', InputArgument::IS_ARRAY, 'Space-separated list of migrations to run down');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Reverting migrations');

        $migrationsFile = base_path('/app/Database/migrations/migrations.php');
        $migrations = include $migrationsFile;
        $migrationsDir = base_path('/app/Database/migrations');

        $database = Database::getInstance();

        $migrationsToRunDown = $input->getArgument('migrations');

        if (empty($migrationsToRunDown)) {
            $io->warning('No migrations specified. Running down all migrations.');
            $migrationsToRunDown = array_keys(array_filter($migrations, fn ($status) => $status === 'applied'));
        }

        foreach ($migrationsToRunDown as $className) {
            if (isset($migrations[$className]) && $migrations[$className] === 'applied') {
                include $migrationsDir . '/' . $className . '.php';
                $migration = new $className();
                $database->query($migration->down());
                $database->query("DELETE FROM migrations WHERE migration = '$className'");
                $migrations[$className] = 'unapplied';
                file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';');
                $io->text("Reverted migration: $className");
            } else {
                $io->warning("Migration not found or not applied: $className");
            }
        }

        $io->success('Migrations reverted successfully');
        return Command::SUCCESS;
    }
}
