<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Handles the registration and dispatching of routes for HTTP requests.
 */
class Router
{
    /**
     * @var array
     */
    private array $routes = [];

    /**
     * Registers a GET route with the specified path and handler.
     *
     * @param string $path
     * @param array $handler
     * @return void
     */
    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Registers a POST route with the specified path and handler.
     *
     * @param string $path The URI path of the POST route to be registered.
     * @param array $handler The handler associated with the route,
     *                       typically a controller and method combination.
     * @return void
     */
    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Handles incoming HTTP requests by matching the requested URI and method to the registered routes.
     * If a matching route is found, the corresponding controller and method are executed.
     * If no match is found, a 404 response is returned.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $path = $_GET['route'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            echo '404 - Page not found';
            return;
        }

        [$controllerClass, $methodName] = $this->routes[$method][$path];

        $controller = new $controllerClass();
        $controller->$methodName();
    }
}
