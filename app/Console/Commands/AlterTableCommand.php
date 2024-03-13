<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "alter:table")]
class AlterTableCommand extends Command
{
    protected static $defaultName = 'alter:table';

    protected function configure()
    {
        $this->setDescription('Alter a table definition.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tableName = $io->ask('Enter the table name', null, function(string $value) {
            if (is_numeric($value) || is_array($value) || is_null($value)) {
                throw new \Exception('Table name must be string');
            }

            return $value;
        });

        $alterationType = $io->choice('Select the alteration type:', [
            'add_column',
            'drop_column',
            'rename_column',
            'change_fieldtype',
            'change_field_size',
            'modify_field',
            'add_constraint'
        ]);

        $initialUpStatement = '';
        $initialDownStatement = '';

        switch ($alterationType) {
            case 'add_column':
                $initialUpStatement = "ALTER TABLE $tableName ADD COLUMN ";
                $initialDownStatement = "ALTER TABLE $tableName DROP COLUMN ";
                break;
            case 'drop_column':
                $initialUpStatement = "ALTER TABLE $tableName DROP COLUMN ";
                $initialDownStatement = "ALTER TABLE $tableName ADD COLUMN ";
                break;
            case 'rename_column':
                $initialUpStatement = "ALTER TABLE $tableName RENAME COLUMN ";
                $initialDownStatement = "ALTER TABLE $tableName RENAME COLUMN ";
                break;
            case 'change_fieldtype':
            case 'change_field_size':
                $initialUpStatement = "ALTER TABLE $tableName ALTER COLUMN ";
                $initialDownStatement = "ALTER TABLE $tableName ALTER COLUMN ";
                break;
            case 'modify_field':
                $initialUpStatement = "ALTER TABLE $tableName MODIFY ";
                $initialDownStatement = "ALTER TABLE $tableName MODIFY ";
                break;
            case 'add_constraint':
                $initialUpStatement = "ALTER TABLE $tableName ADD CONSTRAINT ";
                $initialDownStatement = "ALTER TABLE $tableName DROP CONSTRAINT ";
                break;
            default:
                break;
        }

        $upStatement = $io->ask('Enter the SQL statement for the up operation:', $initialUpStatement, function($value) {
            if (is_numeric($value) || is_array($value) || is_null($value)) {
                throw new \Exception('Up statement must be string');
            }
            if (empty($value)){
                throw new \Exception('Up statement cannot be empty');
            }

            return $value;
        });
        $downStatement = $io->ask('Enter the SQL statement for the down operation:', $initialDownStatement, function($value){
            if(is_numeric($value) || is_array($value) || is_null($value)){
                throw new \Exception('Down statement must be string');
            }
            if(empty($value)){
                throw new \Exception('Down statement cannot be empty');
            }

            return $value;
        });


        $timestamp = date('YmdHis');
        $className = 'AlterTable' . $timestamp . ucfirst($tableName);

        $migrationsDir = base_path('/app/Database/migrations');
        if (!file_exists($migrationsDir)) {
            mkdir($migrationsDir, 0777, true);
        }

        $migrationFile = $migrationsDir . '/' . $className . '.php';

        $content = <<<PHP
            <?php

            class $className
            {
                public function up(): string
                {
                    return "$initialUpStatement $upStatement";
                }

                public function down(): string
                {
                    return "$initialDownStatement $downStatement";
                }
            }
        PHP;

        file_put_contents($migrationFile, $content);
        $io->text("Alteration migration created: $migrationFile");

        $io->text($content);

        $migrationsFile = $migrationsDir . '/migrations.php';
        if (!file_exists($migrationsFile)) {
            file_put_contents($migrationsFile, '<?php ' . PHP_EOL . 'return [];');
        }
        $migrations = include $migrationsFile;
        $migrations[$className] = 'unapplied';

        file_put_contents($migrationsFile, '<?php ' . PHP_EOL . 'return ' . var_export($migrations, true) . ';');

        $io->success("Alteration migration created");

        return Command::SUCCESS;
    }
}
