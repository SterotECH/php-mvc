<?php

namespace App\Controller;

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
        $view = strtok($view, '?');

        if (!empty($data)) {
            extract($data);
        }

        require base_path("resources/views/{$view}.view.php");

    }
}