<?php

namespace App\Console\Commands;

use App\Core\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'make:seeder')]
class MakeSeederCommand extends Command
{
    protected static $defaultName = 'make:seeder';

    protected function configure()
    {
        $this->setDescription('Generate a new seeder class')
        ->addArgument('table', InputArgument::OPTIONAL, 'The table name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tableName = $input->getArgument('table');

        $io = new SymfonyStyle($input, $output);

        if(! $tableName){
            $tableName = $io->ask('Enter Table name');
        }
        $seederClass = ucfirst($tableName) . 'Seeder';
        $seederDir = base_path('/app/Database/seeders');
        if (!file_exists($seederDir)) {
            mkdir($seederDir, 0777, true);
        }
        $seederFile = $seederDir . '/' . $seederClass . '.php';

        $database = Database::getInstance();

        $columnsQuery = match (env('DB_CONNECTION')) {
            'mysql' => "SHOW COLUMNS FROM $tableName",
            'pgsql' => "
            SELECT column_name
            FROM information_schema.columns
            WHERE table_name = '$tableName'
        ",
            default => "SHOW COLUMNS FROM $tableName",
        };
        $columns = $database->query($columnsQuery)->findAll();

        $columnNames = array_column($columns, 'Field');

        $columnNames = array_diff($columnNames, ['id', 'created_at', 'updated_at']);

        $values = [];
        for ($i = 0; $i < 5; $i++) {
            $rowValues = [];
            foreach ($columnNames as $columnName) {
                $rowValues[] = "'Sample $columnName $i'";
            }
            $values[] = '(' . implode(', ', $rowValues) . ')' . PHP_EOL;
        }
        $name = implode(', ', $columnNames);
        $value = implode(', ', $values);

        $sql = "INSERT INTO $tableName ($name)
                    VALUES $value";

        if (!file_exists($seederFile)) {
            $content = <<<PHP
                <?php
                
                namespace App\Database\seeders;

                class $seederClass
                {
                    public function run(): string
                    {

                        return "$sql";

                    }
                }

                PHP;
            file_put_contents($seederFile, $content);
            $io->success("Seeder created: $seederFile");
        } else {
            $io->error("Seeder already exists: $seederFile");
        }

        return Command::SUCCESS;
    }
}
