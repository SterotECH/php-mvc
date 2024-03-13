<?php

namespace App\Console\Commands;

use Dotenv\Dotenv;
use Random\RandomException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "key:generate")]
class KeyGenerateCommand extends Command
{
    protected static $defaultName = 'key:generate';

    protected function configure()
    {
        $this
            ->setDescription('Generate a new application key')
            ->setHelp('This command generates a new application key.');
    }

    /**
     * @throws RandomException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = bin2hex(random_bytes(32));

        $output->writeln([
            'Application Key Generated Successfully',
            '======================================',
            '',
            'Key: ' . $key,
        ]);

        return Command::SUCCESS;
    }

    private function updateEnvFile($keyName, $keyValue)
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../.env');
        $dotenv->required($keyName, $keyValue);
        $dotenv->save();
    }
}