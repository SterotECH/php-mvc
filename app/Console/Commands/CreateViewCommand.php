<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: "create:view")]
class CreateViewCommand extends Command
{
    protected static $defaultName = 'create:view';

    protected function configure()
    {
        $this->setDescription('A command to create a view')
            ->setHelp('This command allows you to create a view')
            ->addArgument('viewName', InputArgument::OPTIONAL, 'The name of the view to create');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $viewName = $input->getArgument('viewName');

        if (!$viewName) {
            $viewName = $io->ask('Enter the name of the view to create');
        }

        $viewPath = base_path('/app/Database/Views');

        if (!file_exists($viewPath)) {
            mkdir($viewPath, 0777, true);
        }

        $viewFile = $viewPath . '/' . $viewName . 'View.php';

        if (file_exists($viewFile)) {
            $io->error('A view with the same name already exists');
            return Command::FAILURE;
        }

        $content = <<<PHP
<?php

namespace App\Database\Views;

class {$viewName}View
{
    public function up(): string
    {
        return "CREATE VIEW {$viewName} AS SELECT * FROM {$viewName};";
    }

    public function down(): string
    {
        return "DROP VIEW {$viewName};";
    }
}

PHP;

        file_put_contents($viewFile, $content);

        $viewFile = $viewPath . '/views.php';
        if (!file_exists($viewFile)) {
            file_put_contents($viewFile, '<?php ' . PHP_EOL . ' return [];');
        }
        $views = include $viewFile;
        $views[$viewName] = 'unapplied';
        file_put_contents($viewFile, '<?php ' . PHP_EOL . 'return ' . var_export($views, true) . ';' . PHP_EOL);

        $io->success("View created successfully $viewFile");

        return Command::SUCCESS;
    }
}
