<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\AuthModel;

class AuthController extends Controller
{
    private AuthModel $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
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
     * Processa os dados do formulário de registro.
     */
    public function register(): void
    {
        $data = $_POST;
        $result = $this->authModel->registerUser($data);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Ativa a conta do usuário a partir do token de verificação.
     */
    public function verifyAccount(): void
    {
        $token = $_GET['token'] ?? null;
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
        $result = $this->authModel->loginUser($email, $senha);

        if ($result['status'] === 'success') {
            // Previne ataques de fixação de sessão
            session_regenerate_id(true);
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $result['user_id'];

            // Redireciona para a página principal
            header('Location: ' . BASE_URL . '/colaboradores');
            exit();
        } else {
            // Se falhar, exibe a página de login com a mensagem de erro.
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
     * Lida com o pedido de recuperação de senha, chamando o Model.
     */
    public function handleForgotPasswordRequest(): void
    {
        $email = $_POST['email'] ?? '';
        $result = $this->authModel->generatePasswordResetToken($email);

        if ($result['status'] === 'success') {
            $this->view('Auth/esqueceuSenha', ['success' => $result['message']]);
        } else {
            $this->view('Auth/esqueceuSenha', ['error' => $result['message']]);
        }
    }

    /**
     * Mostra o formulário para redefinir a senha, se o token for válido.
     */
    public function showResetPasswordForm(): void
    {
        $token = $_GET['token'] ?? '';

        // Verifica se o token é válido antes de mostrar a página
        if ($this->authModel->isPasswordResetTokenValid($token)) {
            $this->view('Auth/redefinirSenha', ['token' => $token]);
        } else {
            // Se o token for inválido ou expirado, mostra uma mensagem de erro
            $this->view('Auth/esqueceuSenha', ['error' => 'O link de redefinição de senha é inválido ou expirou.']);
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

        $result = $this->authModel->resetPassword($token, $novaSenha);

        if ($result['status'] === 'success') {
            // Redireciona para o login com uma mensagem de sucesso
            // (Usaremos a sessão para passar a mensagem)
            $_SESSION['success_message'] = $result['message'];
            header('Location: ' . BASE_URL . '/login');
            exit();
        } else {
            // Mostra o erro na própria página de redefinição
            $this->view('Auth/redefinirSenha', [
                'token' => $token,
                'error' => $result['message']
            ]);
        }
    }
}