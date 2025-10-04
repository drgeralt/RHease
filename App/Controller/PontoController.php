<?php

namespace App\Controller;

/**
 * Controller responsável pelas funcionalidades de Registro de Ponto.
 */
class PontoController
{
    /**
     * Carrega e exibe a página principal de registro de ponto.
     * Este método é o ponto de entrada para a funcionalidade.
     */
    public function index()
    {
        // A única responsabilidade deste método é carregar o arquivo da View.
        // A constante BASE_PATH (definida em seu index.php) nos ajuda a construir
        // o caminho absoluto para o arquivo, evitando erros de 'not found'.
        require_once BASE_PATH . '/App/View/Colaborador/registroPonto.php';
    }
}
