<?php

namespace App\Console\Commands;

use App\Models\User;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "create:superuser")]
class CreateSuperuserCommand extends Command
{
    protected static $defaultName = 'create:superuser';

    protected function configure()
    {
        $this->setDescription('Create a superuser account');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        self::validate($attributes = [], $io);


        unset($attributes['confirm_password']);

        $attributes['password'] = password_hash($attributes['password'], PASSWORD_ARGON2I, [
            'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
        ]);

        $attributes['role'] = 'admin';
        $attributes['is_superuser'] = 1;
        $attributes['other_name'] = '';

        $user = User::create($attributes);

        if (!$user){
            $io->error('Something went wrong');
            Command::INVALID;
        }
        $io->success("{$attributes['username']} Admin account have being activated successfully");

        return Command::SUCCESS;
    }

    public static function validate(array $attributes, $io): array
    {
        $attributes['username'] = $io->ask('Enter username (at least 6 characters)', get_current_user(), function(string $value): string
        {
            if (strlen($value) < 5){
                throw new RuntimeException('username must be at least 6 charters');
            }
            if (is_numeric($value) || is_array($value)){
                throw new RuntimeException('username must be string');
            }
            return $value;
        });
        $exitingUser = User::find('username',$attributes['username']);
        while ($exitingUser->username === $attributes['username']){
            $io->error('User with that username already exist');
            $attributes['username'] = $io->ask('Enter username');
        }
        $attributes['first_name'] = $io->ask('Enter First Name',null, function(string $value): string
        {
            if (strlen($value) < 5){
                throw new RuntimeException('first name must be at least 5 charters');
            }
            if (is_numeric($value) || is_array($value)){
                throw new RuntimeException('first_name must be string');
            }
            return $value;
        });
        $attributes['last_name'] = $io->ask('Enter Last Name', null, function(string $value): string
        {
            if (strlen($value) < 5){
                throw new RuntimeException('last name must be at least 6 charters');
            }
            if (is_numeric($value) || is_array($value)){
                throw new RuntimeException('last name must be string');
            }
            return $value;
        });
        $attributes['email'] = $io->ask('Enter Email',null, function(string $value): string
        {
            if (filter_var($value,FILTER_VALIDATE_EMAIL)){
                throw new RuntimeException('invalid email');
            }
            if (is_numeric($value) || is_array($value)){
                throw new RuntimeException('email must be string');
            }
            return $value;
        });
        $exitingUser = User::find('email',$attributes['email']);
        while ($exitingUser->email === $attributes['email']){
            $io->error('User with that email already exist');
            $attributes['email'] = $io->ask('Enter Email');
        }
        $attributes['phone_number'] = $io->ask('Enter Phone Number', null, function(string $value): string
        {
            if (strlen($value) < 10){
                throw new RuntimeException('phone number must be at least 10 charters');
            }
            if (! is_numeric($value)){
                throw new RuntimeException('phone number must be a numeric value');
            }
            return $value;
        });
        $exitingUser = User::find('phone_number',$attributes['phone_number']);
        while ($exitingUser->phone_number === $attributes['phone_number']){
            $io->error('User with that Phone Number already exist');
            $attributes['phone_number'] = $io->ask('Enter Phone Number');
        }
        $attributes['password'] = $io->askHidden('Enter password');
        $attributes['confirm_password'] = $io->askHidden('Confirm Password');

        while ($attributes['password'] !== $attributes['confirm_password']) {
            $io->error('Password Mismatch retype');
            $attributes['password'] = $io->askHidden('Enter password');
            $attributes['confirm_password'] = $io->askHidden('Confirm Password');
        }

        return $attributes;
    }
}
