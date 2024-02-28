<?php

namespace Commands;

class AlterationCommand
{
    public static function alterTable($tableName, $alterationType, $columnDetails): void
    {
        $timestamp = date('Ymd_His');
        $className = 'AlterTable_' . $timestamp . '_' . ucfirst($tableName);

        $migrationsDir = base_path('/app/Database/migrations');
        if (!file_exists($migrationsDir)) {
            mkdir($migrationsDir, 0777, true);
        }

        $migrationFile = $migrationsDir . '/' . $className . '.php';

        $data = match ($alterationType) {
            'add_column' => "ALTER TABLE $tableName ADD COLUMN $columnDetails",
            'drop_column' => "ALTER TABLE $tableName DROP COLUMN $columnDetails",
            'rename_column' => "ALTER TABLE $tableName RENAME COLUMN $columnDetails",
            'change_fieldtype', 'change_field_size' => "ALTER TABLE $tableName ALTER COLUMN $columnDetails",
            'add_constraint' =>  "ALTER TABLE $tableName ADD CONSTRAINT $columnDetails",
            default => "ALTER TABLE $tableName",
        };

        $content = <<<PHP
<?php
class $className {
    function up()
    {
        return "$data";
    }

    function down(\$db): void
    {
        // Define down method to revert the alterations if needed
    }
}
PHP;

        file_put_contents($migrationFile, $content);

        $migrationsFile = $migrationsDir . '/migrations.php';
        if (!file_exists($migrationsFile)) {
            file_put_contents($migrationsFile, '<?php return [];');
        }
        $migrations = include $migrationsFile;
        $migrations[$className] = 'unapplied';

        file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';');

        echo "\033[0;32mAlteration migration created:\033[0m $migrationFile\n";
    }

}