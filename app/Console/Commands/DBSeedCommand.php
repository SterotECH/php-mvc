<?php

namespace App\Console\Commands;

use App\Core\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TypeError;

#[AsCommand(name: "db:seed")]
class DBSeedCommand extends Command
{
    protected static $defaultName = 'db:seed';

    protected function configure()
    {
        $this->setDescription('Run a seeder class')
            ->addArgument('seeder', InputArgument::OPTIONAL, 'The seeder class name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $seederClass = $input->getArgument('seeder');
        if (!$seederClass) {
            $seederClass = 'Seeder';
            $seederPath = base_path('/app/Database/seeders/' . $seederClass . '.php');

            if (!file_exists($seederPath)) {
                $this->generateSeederClass($seederPath);
                $io->success("Seeder file created: $seederClass.php");
                return Command::SUCCESS;
            }
        } else {
            $seederPath = base_path('/app/Database/seeders/' . $seederClass . '.php');

            if (!file_exists($seederPath)) {
                $io->error("Seeder file not found: $seederClass");
                return Command::FAILURE;
            }
        }

        require_once $seederPath;

        $seederInstance = new $seederClass();
        if (!method_exists($seederInstance, 'run')) {
            $io->error("Seeder class does not have a 'run' method: $seederClass");
            return Command::FAILURE;
        }

        $database = Database::getInstance();
        $query = $seederInstance->run();

        try {
            $database->query($query);
            $io->success("Seeder executed successfully: $seederClass");
        } catch (\Exception $e) {
            $io->error("Error executing seeder: {$e->getMessage()}");
            return Command::FAILURE;
        } catch (TypeError $e){
            $io->success("No Seeder class registered");
            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }

    private function generateSeederClass(string $seederPath): void
    {
        $content = <<<PHP
        <?php
        
        namespace App\Database\seeders;
        
        class Seeder
        {
            public function run(): void
            {
                // Add your seeders here
            }
        }

        PHP;
        file_put_contents($seederPath, $content);
    }
}
