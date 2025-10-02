<?php

namespace App\Controller;

class UserController
{
    // Método para exibir a página de login
    public function show_login()
    {
        require_once BASE_PATH . '/App/View/Auth/login.php';
    }

    // Método para exibir a página de cadastro
    public function show_cadastro()
    {
        require_once BASE_PATH . '/App/View/Auth/cadastro.php';
    }
}