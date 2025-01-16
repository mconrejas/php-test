<?php

namespace App\Core;

class Router {
    private array $routes = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, string|callable $callback): void {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, string|callable $callback): void {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Add a route to the routing table.
     */
    private function addRoute(string $method, string $path, string|callable $callback): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'callback' => $callback,
        ];
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/'); // Normalize path
    
        // Ensure the home page (`/`) is always treated as an empty string or `/`
        if ($path === '' || $path === '/') {
            $path = '/';
        }
    
        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route['path']);
            $pattern = "@^" . $pattern . "$@";
    
            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove the full match
                $this->handleCallback($route['callback'], $matches);
                return;
            }
        }
    
        $this->handleNotFound();
    }
    

    /**
     * Handle route callback (function or "Controller@method").
     */
    private function handleCallback(string|callable $callback, array $params): void {
        if (is_callable($callback)) {
            call_user_func_array($callback, $params);
        } elseif (is_string($callback)) {
            [$controller, $method] = explode('@', $callback);

            $controller = "App\\Controllers\\" . $controller;

            if (class_exists($controller) && method_exists($controller, $method)) {
                $instance = new $controller();
                call_user_func_array([$instance, $method], $params);
            } else {
                throw new \Exception("Controller or method not found: $controller@$method");
            }
        } else {
            throw new \Exception("Invalid route callback");
        }
    }

    /**
     * Handle unmatched routes (404).
     */
    private function handleNotFound(): void {
        http_response_code(404);
        echo "404 - Not Found";
    }
}
