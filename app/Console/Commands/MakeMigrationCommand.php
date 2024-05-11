<?php

namespace App\Console\Commands;

use PHPUnit\Event\Runtime\PHP;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:migration')]
class MakeMigrationCommand extends Command
{
  protected static $defaultName = 'make:migration';

  protected function configure()
  {
    $this->setDescription('Create a new migration file.')
      ->addArgument('name', InputArgument::OPTIONAL, 'The name of the migration.')
      ->setHelp('This command allows you to create a new migration file.')
      ->setDescription('Create a new migration file.');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);
    $timestamp = date('YmdHis');
    $migrationName = $input->getArgument('name');

    if (!$migrationName) {
      $io->title('Create a new migration file');
      $migrationName = $io->ask('Enter the migration name');
    }

    if (!$migrationName) {
      $io->error('Migration name cannot be empty');
      return Command::FAILURE;
    }

    $className = 'Migration' . $timestamp  . ucfirst($migrationName);
    $migrationsDir = base_path('/app/Database/migrations');
    if (!file_exists($migrationsDir)) {
      mkdir($migrationsDir, 0777, true);
    }
    $migrationFile = $migrationsDir . '/' . $className . '.php';
    $id = env('DB_CONNECTION') === 'pgsql' ? 'id SERIAL PRIMARY KEY' : 'id INT AUTO_INCREMENT PRIMARY KEY';
    $content = <<<PHP
<?php

class $className
{
  public function up(): string
  {
    return "CREATE TABLE IF NOT EXISTS $migrationName (
      $id,
      -- add your table definition here
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      --  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
  }

  public function down(): string
  {
      return "DROP TABLE $migrationName";
  }
}

PHP;

    file_put_contents($migrationFile, $content);

    $migrationsFile = $migrationsDir . '/migrations.php';
    if (!file_exists($migrationsFile)) {
      file_put_contents($migrationsFile, '<?php ' . PHP_EOL . ' return [];');
    }
    $migrations = include $migrationsFile;
    $migrations[$className] = 'unapplied';
    file_put_contents($migrationsFile, '<?php ' . PHP_EOL . 'return ' . var_export($migrations, true) . ';'. PHP_EOL);
    $io->success("Migration file $migrationFile with class $className has been created successfully");
    return Command::SUCCESS;
  }
}
