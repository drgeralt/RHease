<?php

class DemissaoController
{
    public function showIniciarForm()
    {
        $funcionarioModel = new FuncionarioModel();
        $data['funcionarios'] = $funcionarioModel->getAllAtivos();
        require_once BASE_PATH . '/app/Views/demissao/iniciar.php';
    }

    public function processar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $funcionario_id = $_POST['funcionario_id'];
            $data_demissao = $_POST['data_demissao'];
            $tipo_demissao = $_POST['tipo_demissao'];
            $motivo = $_POST['motivo'];
            $ferias_vencidas = isset($_POST['ferias_vencidas']) && $_POST['ferias_vencidas'] == '1';

            $demissaoModel = new DemissaoModel();
            $resultado = $demissaoModel->processarDemissao($funcionario_id, $data_demissao, $tipo_demissao, $motivo, $ferias_vencidas);

            if ($resultado) {
                $data['resumo'] = $resultado;
                require_once BASE_PATH . '/app/Views/demissao/resumo.php';
            } else {
                $_SESSION['flash_error'] = "Ocorreu um erro ao processar a demissão.";
                header('Location: ' . BASE_URL . '/demissao');
                exit;
            }
        } else {
            header('Location: ' . BASE_URL . '/demissao');
            exit;
        }
    }

    public function verResumo($funcionarioId)
    {
        $demissaoModel = new DemissaoModel();
        $resumo = $demissaoModel->getResumoPorFuncionarioId($funcionarioId);

        if ($resumo) {
            $data['resumo'] = $resumo;
            require_once BASE_PATH . '/app/Views/demissao/resumo.php';
        } else {
            // Se não encontrar uma demissão para esse funcionário, mostra um erro.
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Resumo da demissão não encontrado para este funcionário.</p>";
        }
    }
}