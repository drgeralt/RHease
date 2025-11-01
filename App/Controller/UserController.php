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
    /**
     * Exibe a página "Esqueceu Senha".
     */
    public function show_esqueceu_senha(): void
    {
        $this->view('Auth/esqueceuSenha');
    }
    public function register(): void
    {
        //var_dump($_POST);
        //die();
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $email = $_POST['email_profissional'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $authModel = new AuthModel();

        $result = $authModel->loginUser($email, $senha);

        if ($result['status'] === 'success') {
            session_regenerate_id(true);

            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['user_perfil'] = $result['user_perfil']; // Armazena o perfil na sessão

            // --- LÓGICA DE REDIRECIONAMENTO CORRETA ---
            if ($_SESSION['user_perfil'] === 'gestor_rh' || $_SESSION['user_perfil'] === 'diretor') {
                // Se for gestor ou diretor, vai para o dashboard principal
                header('Location: ' . BASE_URL . '/inicio');
            } else {
                // Se for colaborador, vai para a página de "Meus Benefícios"
                header('Location: ' . BASE_URL . '/inicio');
            }
            exit();
            // --- FIM DA LÓGICA CORRETA ---

        } else {
            // Se o login falhar, mostra a página de login com a mensagem de erro.
            $this->view('Auth/login', ['error' => $result['message']]);
        }
    }
}