<?php

namespace App\Http\Controllers;

use App\Core\Session;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller
{

    public Database $db;
    protected Validator $validator;
    protected Request $request;

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

        require_once $viewFilePath;
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
