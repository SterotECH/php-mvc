<?php

namespace App\Traits;

use App\Core\Database;
use App\Core\Session;
use App\Models\User;
use DateTime;

trait Validator
{
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

    public static function required(string $value): bool
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

    protected static function nullable(string $field): bool
    {
        return !isset($field) || $field === '';
    }

    public static function min($value, $min): bool
    {
        return strlen($value) >= $min;
    }

    public static function max($value, $max): bool
    {
        return strlen($value) <= $max;
    }

    protected static function string($value): bool
    {
        return is_string($value);
    }

    protected static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    protected static function url(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    public static function date($value): bool
    {
        return (bool)strtotime($value);
    }

    public static function numeric($value): bool
    {
        return is_numeric($value);
    }

    public static function between($value, array $params): bool
    {
        $min = $params[0];
        $max = $params[1];

        return $value >= $min && $value <= $max;
    }

    public static function notIn($value, array $params): bool
    {
        return !in_array($value, $params);
    }

    public static function in($value, array $params): bool
    {
        return in_array($value, $params);
    }

    public static function same(string $value, string $fieldToMatch): bool
    {
        return $value === $fieldToMatch;
    }

    public static function different($value, array $params): bool
    {
        return $value !== $params[0];
    }

    public static function notBetween($value, array $params): bool
    {
        $min = $params[0];
        $max = $params[1];

        return $value < $min || $value > $max;
    }

    public static function alphaNumeric($value): bool
    {
        return ctype_alnum($value);
    }

    public static function unique(string $value, string $table, string $column): bool
    {
        $db = Database::getInstance();

        $result = $db->query("SELECT COUNT(*) as count FROM $table WHERE $column = ?", [$value])->find();

        return $result && $result->count == 0;
    }

    public static function exists(string $value, string $table, string $column): bool
    {
        $db = Database::getInstance();

        $result = $db->query("SELECT COUNT(*) as count FROM $table WHERE $column = ?", [$value])->find();

        return $result && $result->count > 0;
    }

    public static function passwordVerify(string $password, string $username): bool
    {
        $config = require base_path('/config/auth.php');
        $user = User::find('email',$username, ['password']);
        if ($user && password_verify($password, $user->{$config['database']['password']})) {
            return true;
        }

        return false;
    }

    public static function file($value): bool
    {
        return is_file($value);
    }

    public static function image($value): bool
    {
        return self::file($value) && @getimagesize($value);
    }

    public static function object($value): bool
    {
        return is_object($value);
    }

    public static function array($value): bool
    {
        return is_array($value);
    }

    public static function float($value): bool
    {
        return is_float($value) || is_numeric($value);
    }

    public function boolean($value): bool
    {
        return is_bool($value);
    }

    public static function beforeOrEqual($value, $date): bool
    {
        return strtotime($value) <= strtotime($date);
    }

    public static function before($value, $date): bool
    {
        return strtotime($value) < strtotime($date);
    }

    public static function afterOrEqual($value, $date): bool
    {
        return strtotime($value) >= strtotime($date);
    }

    public static function after($value, $date): bool
    {
        return strtotime($value) > strtotime($date);
    }

    public static function dateFormat($value, $format): bool
    {
        $date = DateTime::createFromFormat($format, $value);
        return $date && $date->format($format) === $value;
    }

    protected function addError(string $field, string $rule, ?string $message = null, ?array $params = null): void
    {
        $fieldName = formatColumnName($field);

        $defaultMessages = [
            'required' => "$fieldName is required.",
            'email' => "$fieldName must be a valid email address.",
            'min' => "$fieldName must be at least :min characters.",
            'max' => "$fieldName may not be greater than :max characters.",
            'regex' => "$fieldName is invalid.",
            'nullable' => "$fieldName is required.",
            'string' => "$fieldName must be a string.",
            'url' => "$fieldName must be a valid URL.",
            'phone' => "$fieldName must be a valid phone number.",
            'password' => "$fieldName must be a valid password.",
            'fileType' => "$fieldName must be a valid file type.",
            'maxFileSize' => "$fieldName must be a valid file size.",
            'unique' => "$fieldName must be unique.",
            'exists' => "No matching records found",
            'same' => "$fieldName must be same.",
            'different' => "$fieldName must be different.",
            'in' => "$fieldName must be in.",
            'notIn' => "$fieldName must not be in.",
            'between' => "$fieldName must be between.",
            'notBetween' => "$fieldName must not be between.",
            'date' => "$fieldName must be a date.",
            'dateFormat' => "$fieldName must be a date format.",
            'before' => "$fieldName must be before.",
            'after' => "$fieldName must be after.",
            'beforeOrEqual' => "$fieldName must be before or equal.",
            'afterOrEqual' => "$fieldName must be after or equal.",
            'boolean' => "$fieldName must be a boolean.",
            'numeric' => "$fieldName must be a numeric.",
            'integer' => "$fieldName must be an integer.",
            'float' => "$fieldName must be a float.",
            'array' => "$fieldName must be an array.",
            'object' => "$fieldName must be an object.",
            'file' => "$fieldName must be a file.",
            'image' => "$fieldName must be an image.",
            'alpha' => "$fieldName must be alpha.",
            'alphaNum' => "$fieldName must be alpha numeric.",
            'alphaDash' => "$fieldName must be alpha dash.",
            'alphaNumDash' => "$fieldName must be alpha numeric dash.",
            'alphaNumSpace' => "$fieldName must be alpha numeric space.",
            'alphaNumDashSpace' => "$fieldName must be alpha numeric dash space.",
            'alphaSpace' => "$fieldName must be alpha space.",
            'alphaDashSpace' => "$fieldName must be alpha dash space.",
            'passwordVerify' => "No Matching account found for that email and password"
        ];

        $message = $message ?? $defaultMessages[$rule] ?? "$fieldName is invalid.";

        if ($params !== null) {
            foreach ($params as $key => $value) {
                $message = str_replace(":$key", $value, $message);
            }
        }

        $this->errors[$field][] = $message;
    }
}