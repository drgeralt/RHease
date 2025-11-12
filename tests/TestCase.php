<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PDO;

/**
 * Ela cuida de carregar o .env, conectar ao banco de dados
 * e fornecer métodos de ajuda (helpers).
 */
abstract class TestCase extends BaseTestCase
{
    /** @var ?PDO */
    private static $pdo = null;

    /**
     * Roda antes de CADA teste.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Limpa a sessão antes de cada teste
        $this->clearSession();

        // Limpa o banco de dados (garante que um teste não afete o outro)
        $this->cleanupDatabase();
    }

    /**
     * Conecta ao banco de dados de TESTE (definido no phpunit.xml).
     * Reutiliza a conexão se ela já estiver aberta.
     */
    protected function getConnection(): PDO
    {
        if (self::$pdo) {
            return self::$pdo;
        }

        try {
            $host = $_ENV['DB_HOST'];
            $db   = $_ENV['DB_DATABASE'];
            $user = $_ENV['DB_USERNAME'];
            $pass = $_ENV['DB_PASSWORD'];
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            self::$pdo = new PDO($dsn, $user, $pass, $options);
            return self::$pdo;

        } catch (\PDOException $e) {
            throw new \RuntimeException("Não foi possível conectar ao banco de dados de teste: " . $e->getMessage());
        }
    }

    /**
     * Limpa as tabelas entre os testes para evitar contaminação.
     */
    protected function cleanupDatabase()
    {
        $conn = $this->getConnection();
        $conn->exec("SET FOREIGN_KEY_CHECKS = 0;");

        // Adicione todas as tabelas que seus testes manipulam
        $conn->exec("TRUNCATE TABLE folha_ponto;");
        $conn->exec("TRUNCATE TABLE colaborador;");
        $conn->exec("TRUNCATE TABLE cargo;");
        $conn->exec("TRUNCATE TABLE setor;");
        $conn->exec("TRUNCATE TABLE beneficios_catalogo;");
        $conn->exec("TRUNCATE TABLE colaborador_beneficio;");
        $conn->exec("TRUNCATE TABLE regras_beneficios;");
        $conn->exec("TRUNCATE TABLE vaga;");
        $conn->exec("TRUNCATE TABLE candidato;");
        $conn->exec("TRUNCATE TABLE candidaturas;");

        $conn->exec("SET FOREIGN_KEY_CHECKS = 1;");
    }

    // --- HELPER DE AUTENTICAÇÃO ---

    protected function mockAuthenticatedUser(int $userId)
    {
        // Como seus testes rodam em processos separados,
        // a superglobal $_SESSION é segura para testes.
        $_SESSION['user_id'] = $userId;
    }

    protected function clearSession()
    {
        $_SESSION = [];
    }

    // --- HELPERS DE DADOS (FÁBRICA) ---

    /**
     * Cria um colaborador de teste no banco.
     */
    protected function createTestColaborador(array $overrides = []): array
    {
        $conn = $this->getConnection();

        $defaults = [
            'nome_completo' => 'Colaborador de Teste',
            'email_profissional' => 'teste-' . uniqid() . '@rplease.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT), // Use um hash real
            'id_cargo' => 1,
            'facial_embedding' => null,
            'facial_registered_at' => null
        ];
        $data = array_merge($defaults, $overrides);

        $stmt = $conn->prepare(
            "INSERT INTO colaborador (nome_completo, email_profissional, senha, id_cargo, facial_embedding, facial_registered_at)
             VALUES (:nome_completo, :email_profissional, :senha, :id_cargo, :facial_embedding, :facial_registered_at)"
        );
        $stmt->execute($data);

        $id = $conn->lastInsertId();
        return array_merge(['id_colaborador' => $id], $data);
    }

    /**
     * Cria um registro de ponto de teste no banco.
     */
    protected function createTestPonto(int $colaboradorId, array $overrides = [])
    {
        $conn = $this->getConnection();

        $defaults = [
            'data_hora_entrada' => date('Y-m-d H:i:s'),
            'data_hora_saida' => null,
            'geolocalizacao' => '-10,-48',
            'caminho_foto' => 'storage/test.jpg',
            'ip_address' => '127.0.0.1'
        ];
        // Adiciona o id_colaborador aos dados
        $data = array_merge($defaults, $overrides, ['id_colaborador' => $colaboradorId]);

        // CORREÇÃO: Use um placeholder para id_colaborador
        $stmt = $conn->prepare(
            "INSERT INTO folha_ponto (id_colaborador, data_hora_entrada, data_hora_saida, geolocalizacao, caminho_foto, ip_address)
         VALUES (:id_colaborador, :data_hora_entrada, :data_hora_saida, :geolocalizacao, :caminho_foto, :ip_address)"
        );

        // Agora o $data contém todas as chaves necessárias
        $stmt->execute($data);

        $id = $conn->lastInsertId();
        return array_merge(['id_registro_ponto' => $id], $data);
    }

    /**
     * Retorna um embedding JSON falso (mas com o formato correto de 512).
     */
    protected function getFakeEmbedding(): string
    {
        // Facenet512 espera um array de 512 floats.
        $embedding = array_fill(0, 512, round(lcg_value(), 5));
        return json_encode($embedding);
    }

    /**
     * Retorna uma string base64 de uma imagem 1x1 pixel válida.
     */
    protected function getFakeImageBase64(): string
    {
        // É um GIF 1x1 pixel transparente.
        return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    }
}