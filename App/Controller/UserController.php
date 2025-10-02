<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\UserModel;

class UserController extends Controller
{
    // Método para MOSTRAR a página de login
    public function show_login(): void
    {
        $this->view('Auth/login');
    }

    // Método para MOSTRAR a página de cadastro
    public function show_cadastro(): void
    {
        $this->view('Auth/cadastro');
    }
    // Método para MOSTRAR a página de sucesso do registro
    public function show_registro_sucesso(): void
    {
        $this->view('Auth/registroSucesso');
    }

    // Método para PROCESSAR o registro de um novo usuário (via API)
    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $userModel = new UserModel();
        $result = $userModel->create($data);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }
    // --- NOVO MÉTODO PARA PROCESSAR A VERIFICAÇÃO ---
    public function verify_account(): void
    {
        // Pega o token da URL (ex: /verify?token=...)
        $token = $_GET['token'] ?? null;

        if (!$token) {
            $this->view('Auth/verificacaoResultado', ['success' => false, 'message' => 'Token não fornecido.']);
            return;
        }

        $userModel = new UserModel();
        $resultado = $userModel->activateAccount($token);

        // Exibe a página de resultado da verificação
        $this->view('Auth/verificacaoResultado', $resultado);
    }
}