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
        return empty($field);
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

    public static function date(string $value): bool
    {
        return (bool)strtotime($value);
    }

    public static function numeric($value): bool
    {
        return is_numeric($value);
    }

    public static function between($value, array|string $params): bool
    {
        if (is_string($params)){
            $params = explode(',', $params);
        }
        $min = $params[0];
        $max = $params[1];

        return $value >= $min && $value <= $max;
    }

    public static function notIn($value, array|string $params): bool
    {
        if (is_string($params)){
            $params = explode(',', $params);
        }
        return !in_array($value, $params);
    }

    public static function in($value, array|string $params): bool
    {
        if (is_string($params)){
            $params = explode(',', $params);
        }
        return in_array($value, $params);
    }

    public static function same(string $value, string $fieldToMatch): bool
    {
        return $value === $fieldToMatch;
    }

    public static function different($value, array|string $params): bool
    {
        if (is_string($params)){
            $params = explode(',', $params);
        }
        return $value !== $params[0];
    }

    public static function notBetween($value, array|string $params): bool
    {
        if (is_string($params)){
            $params = explode(',', $params);
        }
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

        $result = $db->query("SELECT COUNT(*) AS count FROM $table WHERE $column = ?", [$value])->find();
        return $result && $result->count > 0;
    }

    public static function passwordVerify(string $password, string $username): bool
    {
        $config = require base_path('/config/auth.php');
        $user = User::find('email', $username, ['password']);
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

    public static function mimes($value, array|string $mimeTypes): bool
    {
        if (is_string($mimeTypes)){
            $mimeTypes = explode(',', $mimeTypes);
        }
        $info = finfo_open(FILEINFO_MIME_TYPE);
        $fileMimeType = finfo_file($info, $value);
        finfo_close($info);

        return in_array($fileMimeType, $mimeTypes);
    }

    public static function svg($value): bool
    {
        return self::mimes($value, ['image/svg+xml']);
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

    public static function time($value): bool
    {
        return strtotime($value) !== false;
    }
}
