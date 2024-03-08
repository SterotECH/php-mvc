<?php

namespace App\Http\Forms;

use App\Core\Request;
use App\Core\Validator;

class LoginForm
{
    protected array $errors = [];

    public function validate(Request $request): bool
    {
        if (!Validator::email($request->input('email'))) {
            $this->errors['email'] = 'Please Provide a valid email';
        }

        if (!Validator::string($request->input('password'))) {
            $this->errors['password'] = 'Password is required';
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}