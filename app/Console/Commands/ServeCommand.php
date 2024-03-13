<?php

namespace App\Console\Commands;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'serve')]
class ServeCommand extends Command
{
    protected function configure()
    {
        $this->setDescription('Start the local development server')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the controller it must be the name of a model');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $host = '127.0.0.1';
        $port = 8000;

        $io = new SymfonyStyle($input, $output);
        $server = sprintf('%s:%d', $host, $port);

        self::displayServerInfo($server, $io);

        exec("php -S $server -t public", $output, $returnCode);

        return Command::SUCCESS;
    }


    private static function displayServerInfo(string $server, $io): void
    {
        $io->success("Server running at $server" . PHP_EOL);
        $io->info("Press Ctrl+C to quit." . PHP_EOL);
    }
}
