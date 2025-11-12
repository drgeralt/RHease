<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    protected array $routes = [];
    protected array $routeParams = [];

    public function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
        ];
    }

    public function match(string $requestMethod, string $requestUri): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $routePathRegex = $this->buildRegex($route['path']);

            if (preg_match($routePathRegex, $requestUri, $matches)) {
                $params = [];
                foreach ($this->routeParams as $key) {
                    if (isset($matches[$key])) {
                        $params[$key] = $matches[$key];
                    }
                }

                return [
                    'controller' => $route['controller'],
                    'action' => $route['action'],
                    'params' => $params
                ];
            }
        }
        return null;
    }

    private function buildRegex(string $path): string
    {
        $this->routeParams = [];
        $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function ($matches) {
            $this->routeParams[] = $matches[1];
            return '(?P<' . $matches[1] . '>[a-zA-Z0-9_-]+)';
        }, $path);

        return '#^' . $pattern . '$#';
    }
}