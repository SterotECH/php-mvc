<?php

namespace App\Controller;

class Controller {

    /**
     * @param string $view
     * @param array $data
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        require base_path('resources/views/' . $view . '.php');

    }
}