<?php
declare(strict_types=1);

namespace tests\Unit\Core;

use App\Core\Database;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testCreateConnectionRetornaInstanciaPDO()
    {
        try {
            $connection = Database::createConnection();
            $this->assertInstanceOf(PDO::class, $connection);
        } catch (PDOException $e) {
            $this->fail("Falha na conexão com o banco de dados de teste: " . $e->getMessage());
        }
    }
}