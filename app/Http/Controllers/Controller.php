<?php

namespace App\Http\Controllers;

use App\Core\Database;

class Controller {

    public Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
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

        require $viewFilePath;
    }
}