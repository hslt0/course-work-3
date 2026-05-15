<?php

namespace App\Core;

class App
{
    protected string $controllerName = 'App\\Controllers\\CoursesController';
    protected Controller $controller;
    protected string $method = 'index';
    protected array $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // 1. Controller
        if (!empty($url[0])) {
            $potentialControllerName = ucfirst($url[0]) . 'Controller';
            $controllerPath = __DIR__ . '/../Controllers/' . $potentialControllerName . '.php';

            if (file_exists($controllerPath)) {
                $this->controllerName = 'App\\Controllers\\' . $potentialControllerName;
                unset($url[0]);
            }
        }

        $this->controller = new $this->controllerName;

        // 2. Method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 3. Parameters
        $this->params = $url ? array_values($url) : [];

        // Call the controller method with parameters
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    protected function parseUrl(): array
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
