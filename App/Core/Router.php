<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Router simples para direcionar requisições para os controllers corretos.
 */
class Router {

    /** @var array<string, mixed>[] Lista de rotas registradas */
    protected array $routes = [];

    /**
     * Registra uma rota.
     *
     * @param string $method O método HTTP (GET, POST, etc.)
     * @param string $path O caminho da URL (ex: /vagas/listar)
     * @param string $controller A classe do controller
     * @param string $action O método a ser chamado no controller
     */
    public function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
        ];
    }

    /**
     * Processa a requisição atual, encontra a rota correspondente e executa a ação do controller.
     */
    public function getRoutes(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = $this->getRequestUri();

        foreach ($this->routes as $route) {
            // Verifica se o método da requisição corresponde ao da rota
            if ($requestMethod !== $route['method']) {
                continue;
            }

            // Compara o caminho da rota com a URI da requisição
            if ($this->matchPath($route['path'], $requestUri)) {
                $controllerName = $route['controller'];
                $action = $route['action'];

                // Verifica se a classe do controller existe antes de instanciar
                if (!class_exists($controllerName)) {
                    $this->abort(500, "Controller class not found: {$controllerName}");
                }

                $controller = new $controllerName();

                // Verifica se o método no controller existe
                if (!method_exists($controller, $action)) {
                    $this->abort(500, "Action not found in controller: {$controllerName}->{$action}");
                }

                $controller->$action();
                return;
            }
        }

        // Se nenhuma rota corresponder, exibe um erro 404
        $this->abort(404, "Página não encontrada.");
    }

    /**
     * Obtém e normaliza a URI da requisição.
     *
     * @return string A URI limpa (ex: /candidatura/formulario)
     */
    private function getRequestUri(): string
    {
        // Usa a variável 'url' do .htaccess se existir
        if (isset($_GET['url'])) {
            return '/' . trim($_GET['url'], '/');
        }

        // Caso contrário, calcula a partir de $_SERVER['REQUEST_URI']
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = dirname($_SERVER['SCRIPT_NAME']);

        // Remove o diretório base (ex: /RHease/public) da URI
        if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        return $uri ?: '/';
    }

    /**
     * Compara o padrão da rota com a URI da requisição de forma flexível.
     * Esta versão trata /caminho e /caminho/ como sendo a mesma rota.
     *
     * @param string $routePath O caminho definido na rota (ex: /candidatura/formulario)
     * @param string $requestUri A URI atual do navegador (ex: /candidatura/formulario)
     * @return bool
     */
    private function matchPath(string $routePath, string $requestUri): bool
    {
        // Normaliza as URIs removendo a barra final, exceto para a rota raiz.
        if ($requestUri !== '/') {
            $requestUri = rtrim($requestUri, '/');
        }
        if ($routePath !== '/') {
            $routePath = rtrim($routePath, '/');
        }

        return $routePath === $requestUri;
    }

    /**
     * Exibe uma página de erro e termina a execução.
     *
     * @param int $code O código de status HTTP (ex: 404, 500)
     * @param string $message A mensagem de erro a ser exibida.
     */
    private function abort(int $code, string $message): void
    {
        http_response_code($code);
        // Pode ser substituído por uma view de erro mais elaborada
        echo "<h1>Erro {$code}</h1>";
        echo "<p>{$message}</p>";
        exit();
    }
}
