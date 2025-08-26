<?php

class BeneficioController
{
    public function index()
    {
        $model = new BeneficioModel();
        $data['beneficios'] = $model->listarTodosBeneficios();
        require_once BASE_PATH . '/app/Views/beneficios/index.php';
    }

    public function create()
    {
        require_once BASE_PATH . '/app/Views/beneficios/criar.php';
    }

    public function store()
    {
        if (empty($_POST['nome_beneficio']) || !is_numeric($_POST['valor_mensal'])) {
            $_SESSION['flash_error'] = "Nome e valor mensal são obrigatórios e o valor deve ser um número.";
            header('Location: ' . BASE_URL . '/beneficios/criar');
            exit;
        }

        $model = new BeneficioModel();
        if ($model->create($_POST)) {
            $_SESSION['flash_success'] = "Benefício criado com sucesso!";
        } else {
            $_SESSION['flash_error'] = "Erro ao criar benefício.";
        }
        header('Location: ' . BASE_URL . '/beneficios');
        exit;
    }

    public function edit($id)
    {
        $model = new BeneficioModel();
        $data['beneficio'] = $model->getById($id);
        require_once BASE_PATH . '/app/Views/beneficios/editar.php';
    }

    public function update($id)
    {
        if (empty($_POST['nome_beneficio']) || !is_numeric($_POST['valor_mensal'])) {
            $_SESSION['flash_error'] = "Nome e valor mensal são obrigatórios e o valor deve ser um número.";
            header('Location: ' . BASE_URL . '/beneficios/editar/' . $id);
            exit;
        }

        $model = new BeneficioModel();
        if ($model->update($id, $_POST)) {
            $_SESSION['flash_success'] = "Benefício atualizado com sucesso!";
        } else {
            $_SESSION['flash_error'] = "Erro ao atualizar benefício.";
        }
        header('Location: ' . BASE_URL . '/beneficios');
        exit;
    }

    public function destroy($id)
    {
        $model = new BeneficioModel();
        if ($model->delete($id)) {
            $_SESSION['flash_success'] = "Benefício excluído com sucesso!";
        } else {
            $_SESSION['flash_error'] = "Erro ao excluir benefício. Verifique se ele não está associado a funcionários.";
        }
        header('Location: ' . BASE_URL . '/beneficios');
        exit;
    }
}