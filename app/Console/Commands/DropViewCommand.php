<?php

namespace App\Console\Commands;

use App\Core\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "drop:view")]
class DropViewCommand extends Command
{
    protected static $defaultName = 'drop:view';

    protected function configure()
    {
        $this->setDescription('Drop a view from the database')
            ->setHelp('This command allows you to drop a view from the database')
            ->addArgument('view_name', InputArgument::OPTIONAL, 'The name of the view to drop');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Dropping Views');

        $viewFile = base_path('/app/Database/views/views.php');
        $views = include $viewFile;
        $viewDir = base_path('/app/Database/views');

        $database = Database::getInstance();


        foreach ($views as $className => $status) {
            if ($status === 'applied') {
                include $viewDir . '/' . $className . '.php';
                $views = new $className();
                $database->query($views->down());
                $database->query("DELETE FROM views WHERE views = '$className'");
                $views[$className] = 'unapplied';
                file_put_contents($viewFile, '<?php return ' . var_export($views, true) . ';');
                $io->text("Drop View: $className");
            }
        }

        $io->success('Views Dropped!');

        return Command::SUCCESS;
    }
}
