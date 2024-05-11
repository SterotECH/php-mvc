<?php

namespace App\Core;

use App\Core\Request;
use App\Core\Validator;

abstract class FormRequest extends Request
{
    protected array $errors = [];

    abstract public function rules(): array;

    abstract public function authorize(): bool;

    public function validate(array $data): void
    {
        $validator = new Validator();
        $validator->validate($this->all(), $this->rules());
        $this->errors = $validator->errors();
    }

    public function failed(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
