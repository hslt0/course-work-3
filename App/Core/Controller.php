<?php

namespace App\Core;

abstract class Controller
{
    /**
     * Render a view and pass data to it.
     */
    protected function view(string $view, array $data = []): void
    {
        // Extract data array to variables so they can be accessed in the view
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View does not exist.");
        }
    }
}
