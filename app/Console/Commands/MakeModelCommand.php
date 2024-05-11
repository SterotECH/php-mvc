<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: "make:model")]
class MakeModelCommand extends Command
{
    protected static $defaultName = 'make:model';

    protected function configure()
    {
        $this->setDescription('Generate a model class')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the table on which this model is based on');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $modelName = $input->getArgument('name');
        if(! $modelName){
            $modelName = $io->ask('Enter the name of the table that this model is based on');
        }
        $fileName = ucfirst($modelName) . '.php';
        $filePath = base_path('/app/Models/') . $fileName;

        if (file_exists($filePath)) {
            $output->writeln("Model '$modelName' already exists.");
            return Command::FAILURE;
        }

        $modelContent =
        <<<PHP
            <?php

            namespace App\Models;

            class $modelName extends Model
            {

                protected static array \$fields = [
                    'id'
                ];
            }
        PHP;

        file_put_contents($filePath, $modelContent);

        $output->writeln("Model '$modelName' created successfully at: $filePath");

        return Command::SUCCESS;
    }
}
