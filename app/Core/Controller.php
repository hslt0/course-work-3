<?php

namespace App\Core;

abstract class Controller
{
    public function __construct()
    {
        if (RateLimiter::check('global_page_load', 60, 60)) {
            http_response_code(429);
            die('Too many requests. Please slow down and try again shortly.');
        }
        RateLimiter::attempt('global_page_load', 60, 60);
    }

    protected function requireAdmin(): void
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("Access Denied: You do not have permission to perform this action or view this page.");
        }
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View does not exist.");
        }
    }
}