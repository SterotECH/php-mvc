<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:command')]
class MakeCommandCommand extends Command
{
    protected static $defaultName = 'make:command';

    protected function configure()
    {
        $this->setDescription('Generate a new command class')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        if (!$name) {
            $io->title('Create a new command');
            $name = $io->ask('What is the name of the command?');
        }
        $io->success("Command name: $name");
        $className = str_replace(' ', '', ucwords(str_replace(':', ' ', $name))) . 'Command';
        $filePath = base_path("/app/Console/Commands");

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $fileName = "$filePath/$className.php";

        if (file_exists($fileName)) {
            $output->writeln("<error>Command already exists:</error> $className");

            return Command::FAILURE;
        }


        $content = <<<PHP
<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "$name")]
class $className extends Command
{
    protected static \$defaultName = '$name';

    protected function configure()
    {
        \$this->setDescription('TODO: Write a description for the command');
    }

    protected function execute(InputInterface \$input, OutputInterface \$output): int
    {
        \$output->writeln('TODO: Implement the execute() method');

        return Command::SUCCESS;
    }
}
PHP;

        file_put_contents($fileName, $content);

        $output->writeln("<info>Command created:</info> $className");

        return Command::SUCCESS;
    }
}
