<?php

namespace App\Core\Exceptions;

class ValidationExceptions extends \Exception
{
    public readonly array $errors;

    public readonly mixed  $old;

    public static function throw($errors, $old )
    {
        $instance = new static;

        $instance->errors = $errors;
        $instance ->old = $old;

        return $instance;
    }
}
