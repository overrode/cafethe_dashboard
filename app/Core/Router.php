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
     * @param string $path
     * @param array $handler
     * @return void
     */
    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
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
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = '/public';

        $path = str_replace($basePath, '', $requestUri);
        $path = $path === '' ? '/' : $path;

        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            echo '404 - Page not found';
            return;
        }

        /**
         * The fully qualified name of the controller class.
         *
         * This variable typically holds the name of a controller class
         * that will be instantiated and used for handling a specific
         * action or request in the application.
         *
         * It is expected to be a string representing a valid class name
         * that adheres to the project's naming conventions and structure.
         */
        [$controllerClass, $methodName] = $this->routes[$method][$path];

        $controller = new $controllerClass();
        $controller->$methodName();
    }
}
