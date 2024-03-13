<?php

namespace App\Http\Controllers;

use Exception;
use App\Core\Router;
use App\Core\Session;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Exceptions\ValidationExceptions;
use App\Core\Validator;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller
{

    public Database $db;
    protected array $errors = [];
    protected Validator $validator;
    protected Request $request;
    public array $old = [];
    public array $rules = [];

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        if (empty(Session::get('csrf_token'))) {
            Session::put('csrf_token', bin2hex(random_bytes(32)));
        }
        $this->request = new Request;
        $this->validateCSRFToken($this->request);
    }

    /**
     * @param string $view
     * @param array $data
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        $viewFilePath = base_path("resources/views/$view.view.php");

        if (!file_exists($viewFilePath)) {
            abort(description: "resources/views/$view.view.php do not exist");
        }

        if (!empty($data)) {
            extract($data);
        }

        $data['errors'] = $this->errors;
        $data['old'] = $this->old;

        require_once $viewFilePath;
    }

    protected function validate(array $old, array $rules): void
    {
        $this->old = $old;
        $this->rules = $rules;

        $this->validator = new Validator();

        $this->validator->validate(data: $old, rules: $rules);
        $this->errors = $this->validator->errors();
//        dd($this->failed());
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

    public function error(string $fields, string $message): static
    {
        $this->errors[$fields] = $message;
        return $this;
    }

    public function throwValidationException(): void
    {
        $this->handleValidationException(ValidationExceptions::throw($this->errors, $this->old));
    }

    /**
     * Validate CSRF token.
     */
    private function validateCSRFToken(): void
    {
        $csrfToken = $this->request->input('csrf_token');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
            ($csrfToken === null || $csrfToken !== Session::get('csrf_token'))
        ) {
            abort(Response::HTTP_FORBIDDEN, 'Invalid CSRF token');
        }
    }
}
