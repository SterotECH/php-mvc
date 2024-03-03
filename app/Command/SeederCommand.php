<?php

namespace App\Command;


use App\Core\Database;

class SeederCommand
{
    public static function makeSeeder(string $tableName): void
    {
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
            default => '"SHOW COLUMNS FROM $tableName"',
        };
        $columns = $database->query($columnsQuery)->findAll();

        $columnNames = array_column($columns, 'Field');

        $values = [];
        for ($i = 0; $i < 10; $i++) {
        $rowValues = [];
            foreach ($columnNames as $columnName) {
            $rowValues[] = "'Sample $columnName $i'";
            }
            $values[] = '(' . implode(', ', $rowValues) . ')';
        }
        $name = implode(', ', $columnNames);
        $value = implode(', ', $values);

        $sql = "INSERT INTO $tableName ($name) VALUES $value";

        if (!file_exists($seederFile)) {
            $content = <<<PHP
<?php

class $seederClass
{
    public function run(): string
    {

        return "$sql";
        
    }
}
PHP;
            file_put_contents($seederFile, $content);
            echo "\033[0;32mSeeder created:\033[0m $seederFile\n";
        } else {
            echo "\033[0;31mSeeder already exists:\033[0m $seederFile\n";
        }
    }
}
