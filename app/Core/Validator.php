<?php
namespace App\Core;

class Validator
{
    public static function string(mixed $value, int $min = 1, float $max = INF): bool
    {
        $value = strlen(trim((string) $value));
        return $value >= $min && $value <= $max;
    }

//    public static function email(string $value): bool
//    {
//        return filter_var(trim($value), FILTER_VALIDATE_EMAIL);
//    }
//
//    public static function strongPassword(string $password): bool
//    {
//        $uppercase = preg_match('/[A-Z]/', $password);
//        $lowercase = preg_match('/[a-z]/', $password);
//        $number = preg_match('/[0-9]/', $password);
//        $symbol = preg_match('/[^a-zA-Z0-9\s]/', $password);
//
//        return $uppercase && $lowercase && $number && $symbol && strlen($password) >= 8;
//    }
//
//    public static function url(string $url): bool
//    {
//        return filter_var(trim($url), FILTER_VALIDATE_URL);
//    }

    public static function regex(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value);
    }

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
                    break 2;
                }
            }
        }

        return $valid;
    }

    public function required($value): bool
    {
        return !empty($value);
    }

    public function min($value, $min): bool
    {
        return strlen($value) >= $min;
    }

    public function max($value, $max): bool
    {
        return strlen($value) <= $max;
    }
    public function nullable($value): bool
    {
        return true; // Always return true for nullable fields
    }

    public function email($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function strongPassword($value): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d\s]).{8,}$/', $value);
    }

    public function url($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

}
