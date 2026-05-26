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

    /**
     * Render a view, optionally returning the output as a string instead of echoing it.
     */
    protected function view(string $view, array $data = [], bool $return = false): ?string
    {
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            ob_start();
            require_once $viewPath;
            $content = ob_get_clean();
            
            if ($return) {
                return $content;
            }
            
            echo $content;
            return null;
        } else {
            die("View does not exist.");
        }
    }
}