<?php

namespace App\Core;

class Validator
{
    protected array $errors = [];

    public static function fileType(string $filename, array $allowed_types): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $allowed_types);
    }

    public static function maxFileSize(string $file_path, int $max_size = 2097152): bool
    {
        $file_size = filesize($file_path);
        return $file_size > $max_size;
    }

    public function validate(array $data, array $rules): bool
    {
        $valid = true;

        foreach ($rules as $field => $rule) {
            $rulesArray = explode('|', $rule);

            foreach ($rulesArray as $singleRule) {
                $params = explode(':', $singleRule);
                $methodName = $params[0];
                $params = isset($params[1]) ? explode(',', $params[1]) : [];

                if (!$this->$methodName($data[$field], ...$params)) {
                    $valid = false;
                    $this->addError($field, $methodName, null, ['min' => $params[0] ?? null, 'max' => $params[1] ?? null]);
                    break 2;
                }
            }
        }

        return $valid;
    }

    public function required(string $value): bool
    {
        return !empty($value);
    }

    public static function password(string $value): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d\s]).{8,}$/', $value);
    }

    public static function phone(string $value): bool
    {
        $pattern = '/^(?:\+?\d{1,2}[ -]?)?\(?\d{3}\)?[ -]?(\d{3})[ -]?(\d{4})$/';

        return preg_match($pattern, $value);
    }

    public static function regex(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value);
    }

    protected function nullable(string $field): bool
    {
        return !isset($field) || $field === '';
    }

    public function min($value, $min): bool
    {
        return strlen($value) >= $min;
    }

    public function max($value, $max): bool
    {
        return strlen($value) <= $max;
    }

    protected function string(string $value): bool
    {
        return is_string($value);
    }

    protected function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    protected function url(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    public function date($value): bool
    {
        return (bool)strtotime($value);
    }

    public function numeric($value): bool
    {
        return is_numeric($value);
    }

    public function between($value, array $params): bool
    {
        $min = $params[0];
        $max = $params[1];

        return $value >= $min && $value <= $max;
    }

    public function notIn($value, array $params): bool
    {
        return !in_array($value, $params);
    }

    public function in($value, array $params): bool
    {
        return in_array($value, $params);
    }

    public function same(string $value, string $fieldToMatch): bool
    {
        return $value === $fieldToMatch;
    }

    public function different($value, array $params): bool
    {
        return $value !== $params[0];
    }

    public function notBetween($value, array $params): bool
    {
        $min = $params[0];
        $max = $params[1];

        return $value < $min || $value > $max;
    }

    public function alphaNumeric($value): bool
    {
        return ctype_alnum($value);
    }

    public function unique(string $value, string $table, string $column): bool
    {
        $db = Database::getInstance();

        $result = $db->query("SELECT COUNT(*) as count FROM $table WHERE $column = ?", [$value])->find();

        return $result && $result->count == 0;
    }

    protected function addError(string $field, string $rule, ?string $message = null, ?array $params = null): void
    {
        $defaultMessages = [
            'required' => "The $field field is required.",
            'email' => "The $field field must be a valid email address.",
            'min' => "The $field field must be at least :min characters.",
            'max' => "The $field field may not be greater than :max characters.",
            'regex' => "The $field field is invalid.",
            'nullable' => "The $field field is required.",
            'string' => "The $field field must be a string.",
            'url' => "The $field field must be a valid URL.",
            'phone' => "The $field field must be a valid phone number.",
            'password' => "The $field field must be a valid password.",
            'fileType' => "The $field field must be a valid file type.",
            'maxFileSize' => "The $field field must be a valid file size.",
            'unique' => "$field must be unique.",
            'exists' => "The $field field must be exists.",
            'same' => "The $field field must be same.",
            'different' => "The $field field must be different.",
            'in' => "The $field field must be in.",
            'notIn' => "The $field field must not be in.",
            'between' => "The $field field must be between.",
            'notBetween' => "The $field field must not be between.",
            'date' => "The $field field must be a date.",
            'dateFormat' => "The $field field must be a date format.",
            'before' => "The $field field must be before.",
            'after' => "The $field field must be after.",
            'beforeOrEqual' => "The $field field must be before or equal.",
            'afterOrEqual' => "The $field field must be after or equal.",
            'boolean' => "The $field field must be a boolean.",
            'numeric' => "The $field field must be a numeric.",
            'integer' => "The $field field must be an integer.",
            'float' => "The $field field must be a float.",
            'array' => "The $field field must be an array.",
            'object' => "The $field field must be an object.",
            'file' => "The $field field must be a file.",
            'image' => "The $field field must be an image.",
            'alpha' => "The $field field must be alpha.",
            'alphaNum' => "The $field field must be alpha numeric.",
            'alphaDash' => "The $field field must be alpha dash.",
            'alphaNumDash' => "The $field field must be alpha numeric dash.",
            'alphaNumSpace' => "The $field field must be alpha numeric space.",
            'alphaNumDashSpace' => "The $field field must be alpha numeric dash space.",
            'alphaSpace' => "The $field field must be alpha space.",
            'alphaDashSpace' => "The $field field must be alpha dash space.",
        ];

        $message = $message ?? $defaultMessages[$rule] ?? "The $field field is invalid.";

        if ($params !== null) {
            foreach ($params as $key => $value) {
                $message = str_replace(":$key", $value, $message);
            }
        }

        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
