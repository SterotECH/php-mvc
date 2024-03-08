<?php

namespace App\Console\Commands;

use App\Core\Database;

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
        $database = Database::getInstance();
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

    public static function refresh(): void
    {
        echo "Database refreshed.\n";
    }

    public static function down(): void
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
                $database = Database::getInstance();
                $database->query($migration->down());
                $database->query("DELETE FROM migrations WHERE migration = '$className'");
                $migrations[$className] = 'unapplied'; // Update status
                file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';');
                echo "\033[0;32mReverted migration:\033[0m $className\n";
            }
        }

        echo "\033[0;32mAll migrations reverted.\033[0m\n";
    }

    public static function status(): void
    {
        $migrationsFile = base_path('/app/Database/migrations/migrations.php');
        $migrations = include $migrationsFile;

        foreach ($migrations as $className => $status) {
            if ($status === 'applied') {
                echo "[✓] $className\n";
            } else {
                echo "\033[31m[ ]\033[0m $className\n";
            }
        }
    }

   public static function deleteMigration(): void
    {
        $migrationsFile = base_path('/app/Database/migrations/migrations.php');
        $migrations = include $migrationsFile;
        $migrationsDir = base_path('/app/Database/migrations');

        echo "Available migrations:\n";
        foreach ($migrations as $migrationNumber => $status) {
            $statusColor = $status === 'applied' ? "\033[0;32m" : "\033[0;31m";
            $statusMark = $status === 'applied' ? '√' : ' ';
            echo "[$statusColor$statusMark\033[0m]  $migrationNumber\n";
        }

        echo "Enter the migrations to delete (comma-separated): ";
        $input = trim(fgets(STDIN));
        $selectedMigrations = explode(',', $input);

        foreach ($selectedMigrations as $selectedMigration) {
            if (isset($migrations[trim($selectedMigration)])) {
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

                    echo "\033[0;32mUndone and deleted migration:\033[0m $selectedMigration\n";
                }

                $migrationFile = $migrationsDir . '/' . $selectedMigration . '.php';
                unlink($migrationFile);

                unset($migrations[$selectedMigration]);

                echo "\033[0;32mDeleted migration:\033[0m $selectedMigration\n";
            } else {
                echo "\033[0;31mMigration not found:\033[0m $selectedMigration\n";
            }
        }

        file_put_contents($migrationsFile, '<?php return ' . var_export($migrations, true) . ';');
    }

}

