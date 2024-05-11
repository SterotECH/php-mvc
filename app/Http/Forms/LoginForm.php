<?php

namespace App\Http\Forms;

use App\Core\ValidationExceptions;
use App\Core\Validator;

class LoginForm
{
    protected array $errors = [];

    public function __construct(public array $attributes)
    {
        if (!Validator::email($attributes['email'])) {
            $this->errors['email'] = 'Please Provide a valid email';
        }

        if (!Validator::string($attributes['password'], 100)) {
            $this->errors['password'] = 'Password is required';
        }
    }


    public static function validate(array $attributes): null|static
    {
        $instance = new static($attributes);

        if ($instance->failed()) {
            $instance->throw();
        }

        return $instance;
    }

    public function throw(): void
    {
        ValidationExceptions::throw($this->errors(), $this->attributes);
    }

    public function failed(): bool
    {
        return count($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function error(string $fields, string $message): static
    {
        $this->errors[$fields] = $message;
        return $this;
    }
}
