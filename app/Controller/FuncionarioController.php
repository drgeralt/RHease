<?php

class FuncionarioController
{
    public function listarDemitidos()
    {
        $model = new FuncionarioModel();
        $data['funcionarios_demitidos'] = $model->getAllInativos();
        
        require_once BASE_PATH . '/app/Views/funcionarios/demitidos.php';
    }
}