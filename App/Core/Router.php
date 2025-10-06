<?php
declare(strict_types=1);

namespace App\Core;

class Router {
    
    /** @var array<string, mixed>[] Lista de rotas registradas */
    protected array $routes = [];

    /**
     * Registra uma rota.
     *
     * @param string $method GET|POST
     * @param string $path caminho da rota (ex: /vagas/listar)
     * @param string $controller Classe do controller
     * @param string $action Método a chamar
     */
    public function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'path' => $path,
            'controller' => $controller,
            'method' => $method,
            'action' => $action,
        ];
    }

    /**
     * Percorre as rotas registradas e executa a que casar com a requisição.
     */
    public function getRoutes(): void
    {
        $request_method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (isset($_GET['url']) && !empty($_GET['url'])) {
            $request_uri = '/' . ltrim((string) $_GET['url'], '/');
        } else {
            $request_uri = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

            $base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
            if ($base_path !== '/' && strpos($request_uri, $base_path) === 0) {
                $request_uri = substr($request_uri, strlen($base_path));
            }

            if (empty($request_uri)) {
                $request_uri = '/';
            }
        }

        foreach ($this->routes as $route) {
            $route_pattern = preg_replace('/\{([a-zA-Z0-9_]+)}/', '(?P<$1>[a-zA-Z0-9_]+)', $route['path']);
            $route_pattern = '#^' . $route_pattern . '$#';

            $comp_uri = '/' . ltrim($request_uri, '/');

            if (preg_match($route_pattern, $comp_uri, $matches) && $request_method === $route['method']) {
                $controller_name = $route['controller'];
                $action = $route['action'];
                $controller = new $controller_name();
                array_shift($matches);
                $controller->$action(...array_values($matches));
                return;
            }
        }

        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>The requested page could not be found.</p>";
    }
}