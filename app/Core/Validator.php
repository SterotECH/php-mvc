<?php

namespace App\Core;

class Validator
{
    use \App\Traits\Validator;

    protected array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $valid = true;

        foreach ($rules as $field => $rule) {
            $rulesArray = explode('|', $rule);

            foreach ($rulesArray as $singleRule) {
                $params = explode(':', $singleRule);
                $methodName = $params[0];
                $params = isset($params[1]) ? explode(',', $params[1]) : [];

                if ($methodName === 'nullable') {
                    continue;
                }

                if (!$this->$methodName($data[$field], ...$params)) {
                    $valid = false;
                    $message = $params[2] ?? null;
                    $this->addError($field, $methodName, $message, ['min' => $params[0] ?? null, 'max' => $params[0] ?? null, 'params' => $params[0] ?? null]);
                }
            }
        }

        return $valid;
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
            'in' => "$fieldName must be in :params.",
            'notIn' => "$fieldName must not be in:params.",
            'between' => "$fieldName must be between.",
            'notBetween' => "$fieldName must not be between.",
            'date' => "$fieldName must be a date.",
            'dateFormat' => "$fieldName must be a date format.",
            'before' => "$fieldName must be before.",
            'after' => "$fieldName must be after.",
            'beforeOrEqual' => "$fieldName must be before or equal. :params",
            'afterOrEqual' => "$fieldName must be after or equal.:params",
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


    public function errors(): array
    {
        return $this->errors;
    }

    public function clearErrors(): void
    {
        $this->errors = [];
    }

}
