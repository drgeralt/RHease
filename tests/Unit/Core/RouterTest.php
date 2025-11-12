<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Router;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();

        $this->router->addRoute('GET', '/', 'App\Controller\HomeController', 'index');
        $this->router->addRoute('POST', '/login', 'App\Controller\AuthController', 'processLogin');
        $this->router->addRoute('GET', '/colaboradores', 'App\Controller\ColaboradorController', 'listar');
        $this->router->addRoute('GET', '/colaboradores/editar/{id}', 'App\Controller\ColaboradorController', 'editar');
    }

    public function testMatchEncontraRotaCorretaSimples()
    {
        $route = $this->router->match('GET', '/');

        $this->assertIsArray($route);
        $this->assertEquals('App\Controller\HomeController', $route['controller']);
        $this->assertEquals('index', $route['action']);
    }

    public function testMatchNaoEncontraRotaMetodoIncorreto()
    {
        $route = $this->router->match('POST', '/');
        $this->assertNull($route);
    }

    public function testMatchNaoEncontraRotaURIIncorreta()
    {
        $route = $this->router->match('GET', '/pagina-que-nao-existe');
        $this->assertNull($route);
    }

    public function testMatchEncontraRotaComParametros()
    {
        $route = $this->router->match('GET', '/colaboradores/editar/123');

        $this->assertIsArray($route);
        $this->assertEquals('App\Controller\ColaboradorController', $route['controller']);
        $this->assertEquals('editar', $route['action']);
        $this->assertArrayHasKey('params', $route);
        $this->assertEquals(['id' => '123'], $route['params']);
    }

    public function testMatchNaoEncontraRotaComParametroFaltando()
    {
        $route = $this->router->match('GET', '/colaboradores/editar/');
        $this->assertNull($route);
    }
}