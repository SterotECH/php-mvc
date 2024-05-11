<?php

namespace App\Console\Commands;

use App\Core\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "apply:view")]
class ApplyViewsCommand extends Command
{
    protected static $defaultName = 'apply:view';

    protected function configure()
    {
        $this->setDescription('Apply views to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->Note('Running Views');

        $viewFile = base_path('/app/Database/views/views.php');
        $views = include $viewFile;
        $viewDir = base_path('/app/Database/views');

        $database = Database::getInstance();
        $id = env('DB_CONNECTION') === 'pgsql' ? 'id SERIAL PRIMARY KEY' : 'id INT AUTO_INCREMENT PRIMARY KEY';
        $database->query("
            CREATE TABLE IF NOT EXISTS views (
                $id,
                view_name VARCHAR(255) NOT NULL,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");



        foreach ($views as $className => $status) {
            if ($status === 'unapplied') {
                include $viewDir . '/' . $className . '.php';
                $views = new $className();
                $database->query($views->up());
                $database->query("INSERT INTO views (view_name) VALUES ('$className')");
                $views[$className] = 'applied';
                file_put_contents($viewFile, '<?php return ' . var_export($views, true) . ';');
                $io->text("Applied View: $className");
            }
        }

        $io->success('Database migrated!');

        return Command::SUCCESS;
    }
}
