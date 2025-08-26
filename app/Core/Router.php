<?php

class Router {
    protected $routes = [];

    public function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'path' => $path,
            'controller' => $controller,
            'method' => $method,
            'action' => $action,
        ];
    }

    public function getRoutes() {
        $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $request_method = $_SERVER['REQUEST_METHOD'];

        $base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($base_path !== '/') {
            $request_uri = substr($request_uri, strlen($base_path));
        }

        if (empty($request_uri)) {
            $request_uri = '/';
        }

        foreach ($this->routes as $route) {

            $route_pattern = preg_replace('/\{([a-zA-Z0-9_]+)}/', '(?P<$1>[a-zA-Z0-9_]+)', $route['path']);
            $route_pattern = '#^' . $route_pattern . '$#';

            
            if(preg_match($route_pattern, $request_uri, $matches) && $request_method === $route['method']){
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