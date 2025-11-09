<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Model\AuthModel;
use PDO;
use PDOStatement;
use PHPMailer\PHPMailer\PHPMailer;
use ReflectionClass;

/**
 * Testes Unitários para AuthModel
 *
 * Estes testes usam MOCKS do PDO para não depender de banco de dados real.
 */
class AuthModelTest extends TestCase
{
    private $pdoMock;
    private $stmtMock;
    private $authModel;

    /**
     * Executa ANTES de cada teste
     * Prepara os mocks e a instância do AuthModel
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Cria mocks do PDO e PDOStatement
        $this->pdoMock = $this->createMock(PDO::class);
        $this->stmtMock = $this->createMock(PDOStatement::class);

        // Cria uma instância do AuthModel
        $this->authModel = new AuthModel();

        // Injeta o PDO mockado no AuthModel usando Reflection
        $this->injectMockDatabase($this->authModel, $this->pdoMock);
    }

    /**
     * Helper para injetar o mock do PDO no AuthModel
     */
    private function injectMockDatabase($object, $mockDb): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($object, $mockDb);
    }

    /**
     * Helper para acessar método privado (validatePassword)
     */
    private function invokePrivateMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    // ========================================
    // TESTES DE VALIDAÇÃO DE SENHA
    // ========================================

    /** @test */
    public function it_validates_strong_password()
    {
        $result = $this->invokePrivateMethod($this->authModel, 'validatePassword', ['Senha@123']);
        $this->assertTrue($result);
    }

    /** @test */
    public function it_rejects_password_without_uppercase()
    {
        $result = $this->invokePrivateMethod($this->authModel, 'validatePassword', ['senha@123']);
        $this->assertFalse($result);
    }

    /** @test */
    public function it_rejects_password_without_lowercase()
    {
        $result = $this->invokePrivateMethod($this->authModel, 'validatePassword', ['SENHA@123']);
        $this->assertFalse($result);
    }

    /** @test */
    public function it_rejects_password_without_number()
    {
        $result = $this->invokePrivateMethod($this->authModel, 'validatePassword', ['Senha@Abc']);
        $this->assertFalse($result);
    }

    /** @test */
    public function it_rejects_password_without_special_char()
    {
        $result = $this->invokePrivateMethod($this->authModel, 'validatePassword', ['Senha123']);
        $this->assertFalse($result);
    }

    /** @test */
    public function it_rejects_short_password()
    {
        $result = $this->invokePrivateMethod($this->authModel, 'validatePassword', ['Se@1']);
        $this->assertFalse($result);
    }

    // ========================================
    // TESTES DE REGISTRO DE USUÁRIO
    // ========================================

    /** @test */
    public function it_registers_user_successfully()
    {
        $data = [
            'nome_completo' => 'João Silva',
            'cpf' => '12345678909',
            'email_profissional' => 'joao@empresa.com',
            'senha' => 'Senha@123'
        ];

        // Mock do prepare e execute
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Desabilita envio de email usando reflection
        $reflection = new ReflectionClass($this->authModel);
        $method = $reflection->getMethod('sendVerificationEmail');
        $method->setAccessible(true);

        // Mock do PHPMailer
        $mailMock = $this->createMock(PHPMailer::class);
        $mailMock->method('send')->willReturn(true);

        $result = $this->authModel->registerUser($data);

        $this->assertEquals('success', $result['status']);
        $this->assertStringContainsString('sucesso', strtolower($result['message']));
    }

    /** @test */
    public function it_rejects_registration_with_empty_fields()
    {
        $data = [
            'nome_completo' => '',
            'cpf' => '12345678909',
            'email_profissional' => 'joao@empresa.com',
            'senha' => 'Senha@123'
        ];

        $result = $this->authModel->registerUser($data);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('obrigatórios', $result['message']);
    }

    /** @test */
    public function it_rejects_registration_with_weak_password()
    {
        $data = [
            'nome_completo' => 'João Silva',
            'cpf' => '12345678909',
            'email_profissional' => 'joao@empresa.com',
            'senha' => 'senha123' // Senha fraca
        ];

        $result = $this->authModel->registerUser($data);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('segurança', $result['message']);
    }

    /** @test */
    public function it_handles_duplicate_email_on_registration()
    {
        $data = [
            'nome_completo' => 'João Silva',
            'cpf' => '12345678909',
            'email_profissional' => 'joao@empresa.com',
            'senha' => 'Senha@123'
        ];

        // Simula exceção de duplicação (código 23000)
        $exception = new \PDOException('Duplicate entry');
        $exception->errorInfo = [0 => '', 1 => 23000];

        $reflection = new ReflectionClass($exception);
        $property = $reflection->getProperty('code');
        $property->setAccessible(true);
        $property->setValue($exception, 23000);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willThrowException($exception);

        $result = $this->authModel->registerUser($data);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('já cadastrado', $result['message']);
    }

    // ========================================
    // TESTES DE ATIVAÇÃO DE CONTA
    // ========================================

    /** @test */
    public function it_activates_account_with_valid_token()
    {
        $token = 'valid_token_123';
        $tokenHash = hash('sha256', $token);

        // Mock da primeira query (busca o token)
        $this->pdoMock->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->stmtMock);

        // Primeira chamada: busca o usuário
        $this->stmtMock->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);

        // Mock do fetch - retorna usuário válido
        $futureDate = (new \DateTime('+1 hour'))->format('Y-m-d H:i:s');
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id_colaborador' => 1,
                'token_expiracao' => $futureDate
            ]);

        $result = $this->authModel->activateAccount($token);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('ativada com sucesso', $result['message']);
    }

    /** @test */
    public function it_rejects_invalid_token()
    {
        $token = 'invalid_token';
        $tokenHash = hash('sha256', $token);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Token não encontrado
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);

        $result = $this->authModel->activateAccount($token);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('inválido', $result['message']);
    }

    /** @test */
    public function it_rejects_expired_token()
    {
        $token = 'expired_token';
        $tokenHash = hash('sha256', $token);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Token expirado (1 hora atrás)
        $pastDate = (new \DateTime('-1 hour'))->format('Y-m-d H:i:s');
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id_colaborador' => 1,
                'token_expiracao' => $pastDate
            ]);

        $result = $this->authModel->activateAccount($token);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('expirou', $result['message']);
    }

    // ========================================
    // TESTES DE LOGIN
    // ========================================

    /** @test */
    public function it_logs_in_user_successfully()
    {
        $email = 'usuario@empresa.com';
        $senha = 'Senha@123';
        $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Mock do usuário encontrado
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id_colaborador' => 1,
                'senha' => $hashedPassword,
                'status_conta' => 'ativo',
                'perfil' => 'colaborador',
                'failed_login_attempts' => 0,
                'last_failed_login_at' => null,
                'is_locked' => 0,
                'minutes_remaining' => 0
            ]);

        $result = $this->authModel->loginUser($email, $senha);

        $this->assertEquals('success', $result['status']);
        $this->assertEquals(1, $result['user_id']);
        $this->assertEquals('colaborador', $result['user_perfil']);
    }

    /** @test */
    public function it_rejects_login_with_wrong_password()
    {
        $email = 'usuario@empresa.com';
        $senha = 'SenhaErrada@123';
        $correctHash = password_hash('Senha@123', PASSWORD_DEFAULT);

        $this->pdoMock->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);

        // Mock do usuário encontrado
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id_colaborador' => 1,
                'senha' => $correctHash,
                'status_conta' => 'ativo',
                'perfil' => 'colaborador',
                'failed_login_attempts' => 0,
                'last_failed_login_at' => null,
                'is_locked' => 0,
                'minutes_remaining' => 0
            ]);

        $result = $this->authModel->loginUser($email, $senha);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('inválidos', $result['message']);
        $this->assertStringContainsString('4 tentativa', $result['message']); // Restam 4 tentativas
    }

    /** @test */
    public function it_rejects_login_with_nonexistent_email()
    {
        $email = 'naoexiste@empresa.com';
        $senha = 'Senha@123';

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Usuário não encontrado
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);

        $result = $this->authModel->loginUser($email, $senha);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('inválidos', $result['message']);
    }

    /** @test */
    public function it_rejects_login_for_unverified_account()
    {
        $email = 'usuario@empresa.com';
        $senha = 'Senha@123';
        $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Conta pendente de verificação
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id_colaborador' => 1,
                'senha' => $hashedPassword,
                'status_conta' => 'pendente_verificacao',
                'perfil' => 'colaborador',
                'failed_login_attempts' => 0,
                'last_failed_login_at' => null,
                'is_locked' => 0,
                'minutes_remaining' => 0
            ]);

        $result = $this->authModel->loginUser($email, $senha);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('não foi ativada', $result['message']);
    }

    /** @test */
    public function it_blocks_login_after_max_attempts()
    {
        $email = 'usuario@empresa.com';
        $senha = 'Senha@123';
        $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Conta bloqueada
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id_colaborador' => 1,
                'senha' => $hashedPassword,
                'status_conta' => 'ativo',
                'perfil' => 'colaborador',
                'failed_login_attempts' => 5,
                'last_failed_login_at' => date('Y-m-d H:i:s'),
                'is_locked' => 1,
                'minutes_remaining' => 15
            ]);

        $result = $this->authModel->loginUser($email, $senha);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('bloqueada', $result['message']);
        $this->assertStringContainsString('15 minuto', $result['message']);
    }

    // ========================================
    // TESTES DE RECUPERAÇÃO DE SENHA
    // ========================================

    /** @test */
    public function it_validates_password_reset_token_correctly()
    {
        $token = 'valid_reset_token';
        $tokenHash = hash('sha256', $token);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Token válido
        $futureDate = (new \DateTime('+10 minutes'))->format('Y-m-d H:i:s');
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id_colaborador' => 1,
                'token_expiracao' => $futureDate
            ]);

        $result = $this->authModel->isPasswordResetTokenValid($token);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_rejects_expired_password_reset_token()
    {
        $token = 'expired_reset_token';
        $tokenHash = hash('sha256', $token);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Token expirado
        $pastDate = (new \DateTime('-10 minutes'))->format('Y-m-d H:i:s');
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id_colaborador' => 1,
                'token_expiracao' => $pastDate
            ]);

        $result = $this->authModel->isPasswordResetTokenValid($token);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_resets_password_successfully()
    {
        $token = 'valid_token';
        $newPassword = 'NovaSenha@123';

        // Mock isPasswordResetTokenValid
        $tokenHash = hash('sha256', $token);

        $this->pdoMock->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);

        // Primeira chamada: validação do token
        $futureDate = (new \DateTime('+10 minutes'))->format('Y-m-d H:i:s');
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id_colaborador' => 1,
                'token_expiracao' => $futureDate
            ]);

        $result = $this->authModel->resetPassword($token, $newPassword);

        $this->assertEquals('success', $result['status']);
        $this->assertStringContainsString('redefinida com sucesso', $result['message']);
    }

    /** @test */
    public function it_rejects_password_reset_with_weak_password()
    {
        $token = 'valid_token';
        $weakPassword = 'senha123'; // Senha fraca

        $result = $this->authModel->resetPassword($token, $weakPassword);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('segurança', $result['message']);
    }

    /** @test */
    public function it_rejects_password_reset_with_invalid_token()
    {
        $token = 'invalid_token';
        $newPassword = 'NovaSenha@123';

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Token inválido
        $this->stmtMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);

        $result = $this->authModel->resetPassword($token, $newPassword);

        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('inválido ou expirado', $result['message']);
    }
}