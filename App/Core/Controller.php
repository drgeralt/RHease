<?php

namespace App\Core;

class Controller
{
    protected $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
        // Inicia sessão se não estiver iniciada (para garantir acesso ao $_SESSION)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = BASE_PATH . '/App/View/' . $view . '.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // Fallback para debug
            die("View file not found: " . $viewPath);
        }
    }

    // --- SEGURANÇA: BLINDAGEM DE GESTOR ---
    protected function exigirPermissaoGestor(): void
    {
        // 1. Verifica se está logado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // 2. Verifica o perfil
        $perfil = $_SESSION['user_perfil'] ?? 'colaborador';

        // Se NÃO for gestor/admin/diretor, chuta para fora
        if (!in_array($perfil, ['gestor_rh', 'diretor', 'admin'])) {
            // Pode redirecionar para o início ou mostrar erro 403
            header('Location: ' . BASE_URL . '/inicio?msg=acesso_negado');
            exit;
        }
    }
}