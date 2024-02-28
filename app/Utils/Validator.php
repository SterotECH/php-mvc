<?php
namespace Utils;

class Validator
{
    public static function string(mixed $value, int $min = 1, float $max = INF): bool
    {
        $value = strlen(trim((string) $value));
        return $value >= $min && $value <= $max;
    }

    public static function email(string $value): bool
    {
        return filter_var(trim($value), FILTER_VALIDATE_EMAIL);
    }

    public static function strongPassword(string $password): bool
    {
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number = preg_match('/[0-9]/', $password);
        $symbol = preg_match('/[^a-zA-Z0-9\s]/', $password);

        return $uppercase && $lowercase && $number && $symbol && strlen($password) >= 8;
    }

    public static function url(string $url): bool
    {
        return filter_var(trim($url), FILTER_VALIDATE_URL);
    }

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

}
