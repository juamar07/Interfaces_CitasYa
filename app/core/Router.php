<?php
declare(strict_types=1);

namespace App\Core;

use Closure;
use RuntimeException;

final class Router
{
    /** @var array<string, array<string, Closure|string>> */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, Closure|string $handler): void
    {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
    }

    public function post(string $path, Closure|string $handler): void
    {
        $this->routes['POST'][$this->normalizePath($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH) ?? '/');

        $handler = $this->routes[$method][$path] ?? null;
        if ($handler === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        if ($handler instanceof Closure) {
            $handler();
            return;
        }

        if (!is_string($handler)) {
            throw new RuntimeException('Unsupported route handler.');
        }

        [$controllerName, $methodName] = explode('@', $handler);
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        if (!class_exists($controllerClass)) {
            throw new RuntimeException(sprintf('Controller %s not found.', $controllerClass));
        }

        $controller = new $controllerClass();
        if (!method_exists($controller, $methodName)) {
            throw new RuntimeException(sprintf('Method %s::%s not found.', $controllerClass, $methodName));
        }

        $controller->{$methodName}();
    }

    private function normalizePath(?string $path): string
    {
        if ($path === null || $path === '') {
            return '/';
        }

        $path = '/' . trim($path, '/');
        return $path === '/' ? $path : rtrim($path, '/');
    }
}
