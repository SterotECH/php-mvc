<?php

namespace Commands;

class MigrationCommand
{
    public static function makeMigration($migrationName): void
    {
        $timestamp = date('Ymd_His');
        $className = 'Migration_' . $timestamp . '_' . ucfirst($migrationName);

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
    function up(): string
    {
         return "CREATE TABLE IF NOT EXISTS $migrationName (
                    $id,
                   -- add your table definition here
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    --  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
    }

    function down(): string
    {
        return "DROP TABLE $migrationName";
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

        echo "\033[0;32mMigration created:\033[0m $migrationFile";
    }

    public static function migrate(): void
    {
        $migrationsFile = base_path('/app/Database/migrations/migrations.php');
        $migrations = include $migrationsFile;
        $migrationsDir = base_path('/app/Database/migrations');

        // Create migration table if it doesn't exist
        $database = new Database();
        $id = env('DB_CONNECTION') === 'pgsql' ? 'id SERIAL PRIMARY KEY' : 'id INT AUTO_INCREMENT PRIMARY KEY';
        $database->query("CREATE TABLE IF NOT EXISTS migrations (
        $id,
        migration VARCHAR(255) NOT NULL,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

        foreach ($migrations as $className => $status) {
            if ($status === 'unapplied') {
                // Include and run the migration class
                include $migrationsDir . '/' . $className . '.php';
                $migration = new $className();
                $database->query($migration->up());
                $database->query("INSERT INTO migrations (migration) VALUES ('$className')");
                $migrations[$className] = 'applied'; // Update status
                file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';');
                echo "\033[0;32mApplied migration:\033[0m $className\n";
            }
        }

        echo "\033[0;32mAll migrations applied.\033[0m\n";
    }

    public function refresh(): void
    {
        echo "Database refreshed.\n";
    }

    public function down(): void
    {
        $migrationsFile = base_path('/app/Database/migrations/migrations.php');
        $migrations = include $migrationsFile;
        $migrationsDir = base_path('/app/Database/migrations');

        // Delete tables in reverse order of application
        $appliedMigrations = array_reverse($migrations);
        foreach ($appliedMigrations as $className => $status) {
            if ($status === 'applied') {
                include $migrationsDir . '/' . $className . '.php';
                $migration = new $className();
                $database = new Database();
                $database->query($migration->down());
                $database->query("DELETE FROM migrations WHERE migration = '$className'");
                $migrations[$className] = 'unapplied'; // Update status
                file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';');
                echo "\033[0;32mReverted migration:\033[0m $className\n";
            }
        }

        echo "\033[0;32mAll migrations reverted.\033[0m\n";
    }

}

