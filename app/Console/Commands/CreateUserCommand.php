<?php

namespace App\Console\Commands;

use App\Models\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "create:user")]
class CreateUserCommand extends Command
{
    protected static $defaultName = 'create:user';

    protected function configure()
    {
        $this->setDescription('TODO: Write a description for the command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        CreateSuperuserCommand::validate($attributes = [], $io);
        $attributes['other_name'] = $io->ask('Enter Other name');

        unset($attributes['confirm_password']);

        $attributes['password'] = password_hash($attributes['password'], PASSWORD_ARGON2I, [
            'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
        ]);

        $attributes['role'] = 'user';
        $attributes['is_superuser'] = 0;


        $user = User::create($attributes);

        if (!$user){
            $io->error('Something went wrong');
            Command::INVALID;
        }
        $io->success("{$attributes['username']} account have being activated successfully");

        return Command::SUCCESS;
    }
}
