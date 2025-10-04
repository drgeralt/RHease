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
    public function login()
    {
        // Apenas carrega a view do formulário de login
        $this->view('Auth/login');
    }

    /**
     * Processa a tentativa de login do usuário.
     */
    public function processLogin()
    {
        // Verifica se os dados foram enviados via POST (formulário)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email_profissional'] ?? '';
            $senha = $_POST['senha'] ?? '';

            $result = $this->authModel->loginUser($email, $senha);

            if ($result['status'] === 'success') {
                // Se o login for bem-sucedido, inicia a sessão
                // e redireciona para a página principal
                session_start();
                $_SESSION['user_logged_in'] = true;
                // Redireciona para a página "principal"
                header("Location: /tabelaColaborador");
                exit();
            } else {
                // Se o login falhar, exibe a página de login novamente
                // com a mensagem de erro.
                $this->view('Auth/login', ['error' => $result['message']]);
            }
        } else {
            // Se não for POST, apenas redireciona para a página de login
            header("Location: /login");
            exit();
        }
    }
}