<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\AuthModel;

class UserController extends Controller
{
    public function show_login(): void
    {
        $this->view('Auth/login');
    }

    public function show_cadastro(): void
    {
        $this->view('Auth/cadastro');
    }

    public function show_registro_sucesso(): void
    {
        $this->view('Auth/registroSucesso');
    }
    public function register(): void
    {
        $data = $_POST;

        $authModel = new AuthModel();
        $result = $authModel->registerUser($data); // Chamamos o novo método

        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }
    public function verify_account(): void
    {
        $token = $_GET['token'] ?? null;

        // Instancia o AuthModel
        $authModel = new AuthModel();
        $result = $authModel->activateAccount($token);

        // Envia o resultado para a view de verificação
        $this->view('Auth/verificacaoResultado', $result);
    }
    public function process_login(): void
    {
        // Verifica se os dados foram enviados via POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Se não for POST, apenas redireciona para a página de login
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $email = $_POST['email_profissional'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $authModel = new AuthModel();
        $result = $authModel->loginUser($email, $senha);

        if ($result['status'] === 'success') {
            // Se o login for bem-sucedido, iniciamos a sessão
            // e guardamos a informação de que o utilizador está logado.
            session_regenerate_id(true); // Previne ataques de fixação de sessão
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $result['user_id'];

            // Redireciona para a página principal do sistema (ex: tabela de colaboradores)
            header('Location: ' . BASE_URL . '/colaboradores');
            exit();
        } else {
            // Se o login falhar, exibe a página de login novamente
            // com a mensagem de erro.
            $this->view('Auth/login', ['error' => $result['message']]);
        }
    }
}