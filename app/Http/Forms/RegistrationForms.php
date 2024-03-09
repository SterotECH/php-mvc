<?php

namespace App\Http\Forms;

use App\Core\Request;
use App\Core\Validator;
use App\Models\User;

class RegistrationForms
{
    protected array $errors = [];

    public function validate(
        string $username,
        string $email,
        string $phone_number,
        string $first_name,
        string $last_name,
        string $password,
        string $password_confirmation = null
    ): bool
    {
        if (!Validator::string($username, 1, 16)) {
            $this->errors['username'] = 'Username must be at least 1 to 16 characters';
        }
        if (!Validator::string($first_name, 1, 100)) {
            $this->errors['first_name'] = 'First name must be at least 1 to 100 charaters ';
        }

        if (!Validator::string($last_name, 1, 100)) {
            $this->errors['last_name'] = 'Last name must be at least 1 to 100 characters.';
        }

        if (!Validator::email($email)) {
            $this->errors['email'] = 'Invalid email format.';
        }

        if (!Validator::string($password, 8, 32)) {
            $this->errors['password'] = 'Password must be at least 8 characters long.';
        }

        if (!Validator::phone($phone_number)) {
            $this->errors['phone_number'] = 'Invalid phone number format';
        }

        if ($password_confirmation !== null) {
            if ($password !== $password_confirmation) {
                $this->errors['password'] = 'Password Mismatch';
            }
        }

        $existing_email = User::find('email', $email);
        if ($existing_email) {
            $this->errors['email'] = 'A user with the given email already exists';
        }
        $existing_phone = User::find('phone_number', $phone_number);
        if ($existing_phone) {
            $this->errors['phone_number'] = 'A User with the same Phone Number Exists';
        }

        $existing_username = User::find('username', $username);
        if ($existing_username) {
            $this->errors['username'] = 'A User with the same username Exists';
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}