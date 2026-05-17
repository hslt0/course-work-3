<?php

namespace App\Core;

abstract class Controller
{
    public function __construct()
    {
        // Apply global rate limiting for all page loads
        // Allow a high number of requests (e.g., 60) per minute for normal browsing
        if (RateLimiter::check('global_page_load', 60, 60)) {
            http_response_code(429);
            die('Too many requests. Please slow down and try again shortly.');
        }
        RateLimiter::attempt('global_page_load', 60, 60);
    }

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
