<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Model\AuthModel;
use PDO; // Importe o PDO

class AuthController extends Controller
{
    protected AuthModel $authModel;

    /**
     * O construtor agora recebe suas dependências
     * e as passa para a classe pai (Controller).
     */
    public function __construct(AuthModel $authModel, PDO $pdo)
    {
        parent::__construct($pdo);
        $this->authModel = $authModel;
    }

    /**
     * Exibe a página de login.
     */
    public function showLogin(): void
    {
        $this->view('Auth/login');
    }

    /**
     * Exibe a página de cadastro.
     */
    public function showCadastro(): void
    {
        $this->view('Auth/cadastro');
    }

    /**
     * Exibe a página de sucesso após o registro.
     */
    public function showRegistroSucesso(): void
    {
        $this->view('Auth/registroSucesso');
    }

    /**
     * Processa os dados do formulário de registro (API JSON).
     */
    public function register(): void
    {
        $data = $_POST;
        // Usa o Model injetado
        $result = $this->authModel->registerUser($data);

        $this->jsonResponse(true, $result['message'], $result); // Usa o jsonResponse herdado
    }

    /**
     * Ativa a conta do usuário a partir do token de verificação.
     */
    public function verifyAccount(): void
    {
        $token = $_GET['token'] ?? null;
        // Usa o Model injetado
        $result = $this->authModel->activateAccount($token);

        $this->view('Auth/verificacaoResultado', $result);
    }

    /**
     * Processa a tentativa de login do usuário.
     */
    public function processLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $email = $_POST['email_profissional'] ?? '';
        $senha = $_POST['senha'] ?? '';

        // Usa o Model injetado
        $result = $this->authModel->loginUser($email, $senha);

        if ($result['status'] === 'success') {
            session_regenerate_id(true);
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['user_perfil'] = $result['user_perfil'];

            if ($_SESSION['user_perfil'] === 'gestor_rh' || $_SESSION['user_perfil'] === 'diretor') {
                header('Location: ' . BASE_URL . '/inicio');
            } else {
                // Redireciona colaborador para o dashboard de colaborador
                header('Location: ' . BASE_URL . '/inicio');
            }
            exit();
        } else {
            $this->view('Auth/login', ['error' => $result['message']]);
        }
    }

    /**
     * Mostra o formulário para solicitar a recuperação de senha.
     */
    public function showForgotPasswordForm(): void
    {
        $this->view('Auth/esqueceuSenha');
    }

    /**
     * Lida com o pedido de recuperação de senha.
     */
    public function handleForgotPasswordRequest(): void
    {
        $email = $_POST['email'] ?? '';
        // Usa o Model injetado
        $result = $this->authModel->generatePasswordResetToken($email);

        if ($result['status'] === 'success') {
            $this->view('Auth/esqueceuSenha', ['success' => $result['message']]);
        } else {
            $this->view('Auth/esqueceuSenha', ['error' => $result['message']]);
        }
    }

    /**
     * Mostra o formulário para redefinir a senha.
     */
    public function showResetPasswordForm(): void
    {
        $token = $_GET['token'] ?? '';

        // Usa o Model injetado
        if ($this->authModel->isPasswordResetTokenValid($token)) {
            $this->view('Auth/redefinirSenha', ['token' => $token]);
        } else {
            $this->view('Auth/esqueceuSenha', ['error' => 'O link de redefinição é inválido ou expirou.']);
        }
    }

    /**
     * Lida com a atualização da nova senha.
     */
    public function handleResetPassword(): void
    {
        $token = $_POST['token'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        if ($novaSenha !== $confirmarSenha) {
            $this->view('Auth/redefinirSenha', [
                'token' => $token,
                'error' => 'As senhas não coincidem.'
            ]);
            return;
        }

        // Usa o Model injetado
        $result = $this->authModel->resetPassword($token, $novaSenha);

        if ($result['status'] === 'success') {
            $_SESSION['success_message'] = $result['message'];
            header('Location: ' . BASE_URL . '/login');
            exit();
        } else {
            $this->view('Auth/redefinirSenha', [
                'token' => $token,
                'error' => $result['message']
            ]);
        }
    }
}