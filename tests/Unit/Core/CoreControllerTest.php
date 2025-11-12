<?php
declare(strict_types=1);


namespace tests\Unit\Core;

use App\Core\Controller;
use PDO;
use PHPUnit\Framework\TestCase;

class CoreControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        $pdoMock = $this->createMock(PDO::class);

        $this->controller = new class($pdoMock) extends Controller {
            public function formatarValorPublico($valor)
            {
                return $this->formatarValor($valor);
            }
        };
    }

    public function testFormatarValorCorretamente()
    {
        $this->assertEquals('R$ 1.500,50', $this->controller->formatarValorPublico(1500.50));
        $this->assertEquals('R$ 10,00', $this->controller->formatarValorPublico(10));
        $this->assertEquals('', $this->controller->formatarValorPublico(0));
        $this->assertEquals('', $this->controller->formatarValorPublico('texto'));
        $this->assertEquals('', $this->controller->formatarValorPublico(null));
    }
}