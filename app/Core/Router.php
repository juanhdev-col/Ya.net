<?php

namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, array $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $uri, string $method): void {
        $path = parse_url($uri, PHP_URL_PATH);

        if (isset($this->routes[$method][$path])) {
            [$controllerClass, $action] = $this->routes[$method][$path];
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }

        http_response_code(404);
        echo "<h1 style='text-align:center; font-family:sans-serif; margin-top:50px;'>404 - Página no encontrada</h1>";
    }
}