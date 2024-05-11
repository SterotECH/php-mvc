<?php

namespace App\Traits;

use Exception;
use App\Core\Router;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Exceptions\ValidationExceptions;

trait HandlesValidation
{
    protected array $errors = [];
    public array $old = [];

    /**
     * Validate the request data based on the given rules.
     *
     * @param array $rules
     * @throws ValidationException
     */
    public function validate(array $rules)
    {
        $this->$rules = $rules;
        $this->old = (array)$this->all();

        $validator = new Validator();

        $validator->validate(data: (array)$this->all(), rules: $rules);
        $this->errors = $validator->errors();

        if (!$this->failed()) {
            $this->throwValidationException();
        }
    }

    protected function handleValidationException(Exception $exception): void
    {
        if ($exception instanceof ValidationExceptions) {
            Session::flash('errors', $exception->errors);
            Session::flash('old', $exception->old);
            redirect(Router::previousUrl());
            exit;
        }
    }

    protected function failed(): bool
    {
        return empty($this->errors);
    }

    public function throwValidationException(): void
    {
        $this->handleValidationException(ValidationExceptions::throw($this->errors, $this->old));
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function validateFile(array $file, $mimes, int $max_size): bool|null
    {
        $maxFileSize = $max_size * 1024;
        $allowedExtensions = is_array($mimes) ? $mimes : [$mimes];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        return in_array(strtolower($extension), $allowedExtensions) && $file['size'] <= $maxFileSize;
    }
}
