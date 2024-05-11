<?php

namespace App\Console\Commands;

use App\Models\User;
use RuntimeException;
use App\Core\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
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
        $this->setName('create:superuser')
            ->addOption('date-format', 'd', InputOption::VALUE_OPTIONAL, 'Date format (e.g., Y-m-d)', 'Y-m-d')
            ->setDescription('Create a superuser account');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $validator = new Validator();

        $dateFormat = $input->getOption('date-format') ?? 'Y-m-d';
        $io->note("Date format: $dateFormat");

        $fields = [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'phone_number' => 'required|unique:users,phone_number',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => "required|date|dateFormat:$dateFormat",
            'gender' => 'required|string',
        ];

        $retryLimit = 3;
        $hasError = false;

        foreach ($fields as $fieldName => $rules) {
            $attempts = 0;

            while ($attempts < $retryLimit && $hasError === true) {
                $value = $io->ask("Enter $fieldName");
                $validate = $validator->validate(rules: [$fieldName => $rules], data: [$fieldName => $value]);

                if (!$validate) {
                    $io->note($validator->errors()[$fieldName]);
                    $validator->clearErrors();
                    $hasError = true;
                    $attempts++;
                } else {
                    break;
                }
            }

            if ($attempts === $retryLimit && $hasError === true) {
                $io->error("Maximum number of attempts reached for $fieldName. Exiting...");
                $hasError = true;
                return Command::FAILURE;
            }
        }

        if (!$hasError) {
            $password = $io->askHidden('Enter password:');
            $confirmPassword = $io->askHidden('Confirm password:');

            if ($password !== $confirmPassword) {
                throw new RuntimeException('Passwords do not match.');
            }

            $user = new User();

            foreach ($fields as $fieldName => $rules) {
                $user->{$fieldName} = $io->ask("Enter $fieldName:");
            }
            $user->other_name = '';
            $user->address = '';
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->role = User::ADMINISTRATOR;
            $user->is_active = 1;
            $user->is_superuser = 1;

            $user->save();

            $io->success('Superadmin user created successfully.');
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }
}
