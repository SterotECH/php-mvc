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

                if (!$this->$methodName($data[$field], ...$params)) {
                    $valid = false;
                    $this->addError($field, $methodName, null, ['min' => $params[0] ?? null, 'max' => $params[1] ?? null]);
                }
            }
        }

        return $valid;
    }


    public function errors(): array
    {
        return $this->errors;
    }

}
