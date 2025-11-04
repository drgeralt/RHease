<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use PDOException;

class VagaApiController extends Controller
{
    private function jsonResponse(mixed $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    // [API] GET /api/vagas/listar 
    public function listarVagas(): void 
    {
        try {
            $vagaModel = $this->model('GestaoVagas');
            $vagas = $vagaModel->listarVagas();
            $this->jsonResponse($vagas);
        } catch (PDOException $e) {
            $this->jsonResponse(['erro' => 'Erro ao consultar o banco de dados.'], 500);
        }
    }

    // [API] POST /api/vagas/salvar 
    public function salvar(): void
    {
        $dados = json_decode(file_get_contents('php://input'), true);
        
        $erros = []; // Adicione sua lógica de validação aqui...
        $titulo = trim($dados['titulo'] ?? '');
        $departamento = trim($dados['departamento'] ?? '');

        if (empty($titulo) || empty($departamento)) {
            $erros['geral'] = 'Título e Departamento são obrigatórios.';
        }

        if (!empty($erros)) {
            $this->jsonResponse(['sucesso' => false, 'erros' => $erros], 422);
            return;
        }

        try {
            $setorModel = $this->model('Setor');
            $idSetor = $setorModel->findOrCreateByName($departamento);

            $dadosVaga = [
                'titulo_vaga' => $titulo,
                'id_setor' => $idSetor,
                'situacao' => $dados['status'] ?? 'rascunho',
                'descricao_vaga' => $dados['descricao'] ?? null,
                'requisitos_necessarios' => $dados['skills_necessarias'] ?? null,
                'requisitos_recomendados' => $dados['skills_recomendadas'] ?? null,
                'requisitos_desejados' => $dados['skills_desejadas'] ?? null,
                'id_cargo' => null,
            ];

            $vagaModel = $this->model('NovaVaga');
            $novoId = $vagaModel->criarVaga($dadosVaga);

            $this->jsonResponse(['sucesso' => true, 'id_vaga' => $novoId], 201);

        } catch (PDOException $e) {
            $this->jsonResponse(['sucesso' => false, 'erro' => 'Erro ao salvar a vaga no banco.'], 500);
        }
    }

    // [API] GET /api/vagas/editar?id=...
    public function editar(): void
    {
        $idVaga = (int)($_GET['id'] ?? 0);
        if ($idVaga === 0) {
            $this->jsonResponse(['erro' => 'ID da vaga não fornecido.'], 400);
            return;
        }

        try {
            $vagaModel = $this->model('GestaoVagas');
            $vaga = $vagaModel->buscarPorId($idVaga);
            if (!$vaga) {
                $this->jsonResponse(['erro' => 'Vaga não encontrada.'], 404);
                return;
            }
            $this->jsonResponse($vaga);
        } catch (PDOException $e) {
            $this->jsonResponse(['erro' => 'Erro ao consultar o banco de dados.'], 500);
        }
    }

    // [API] POST /api/vagas/atualizar
    public function atualizar(): void
    {
        $dados = json_decode(file_get_contents('php://input'), true);

        $idVaga = (int)($dados['id_vaga'] ?? 0);
        $titulo = trim($dados['titulo'] ?? '');
        $departamento = trim($dados['departamento'] ?? '');

        if ($idVaga === 0 || $titulo === '' || $departamento === '') {
            $this->jsonResponse(['sucesso' => false, 'erro' => 'Dados inválidos.'], 422);
            return;
        }

        try {
            $setorModel = $this->model('Setor');
            $idSetor = $setorModel->findOrCreateByName($departamento);

            $dadosVaga = [
                'titulo_vaga' => $titulo,
                'id_setor' => $idSetor,
                'situacao' => $dados['status'] ?? 'rascunho',
                'descricao_vaga' => $dados['descricao'] ?? null,
                'requisitos_necessarios' => $dados['skills_necessarias'] ?? null,
                'requisitos_recomendados' => $dados['skills_recomendadas'] ?? null,
                'requisitos_desejados' => $dados['skills_desejadas'] ?? null,
            ];

            $vagaModel = $this->model('GestaoVagas');
            $vagaModel->atualizarVaga($idVaga, $dadosVaga);

            $this->jsonResponse(['sucesso' => true, 'mensagem' => 'Vaga atualizada.']);
        } catch (PDOException $e) {
            $this->jsonResponse(['sucesso' => false, 'erro' => 'Erro ao atualizar a vaga no banco.'], 500);
        }
    }

    // [API] GET /api/vagas/excluir?id=...
    public function excluir(): void
    {
        $idVaga = (int)($_GET['id'] ?? 0);
        if ($idVaga === 0) {
            $this->jsonResponse(['erro' => 'ID da vaga não fornecido.'], 400);
            return;
        }
        try {
            $vagaModel = $this->model('GestaoVagas');
            $vagaModel->excluirVaga($idVaga);
            $this->jsonResponse(['sucesso' => true, 'mensagem' => 'Vaga excluída.']);
        } catch (PDOException $e) {
            $this->jsonResponse(['sucesso' => false, 'erro' => 'Erro ao excluir a vaga.'], 500);
        }
    }

    // [API] GET /api/vagas/candidatos?id=... 
    public function verCandidatos()
    {
        // MUDANÇA: Agora usa GET pois é uma requisição de busca
        $idVaga = (int)($_GET['id'] ?? 0); 
        if ($idVaga === 0) {
            $this->jsonResponse(['erro' => 'ID da vaga não fornecido.'], 400);
            return;
        }

        try {
            $vagaModel = $this->model('GestaoVagas');
            $candidaturaModel = $this->model('Candidatura');

            $vaga = $vagaModel->buscarPorId($idVaga);
            $candidatos = $candidaturaModel->buscarPorVaga($idVaga);

            if (!$vaga) {
                $this->jsonResponse(['erro' => 'Vaga não encontrada.'], 404);
                return;
            }
            
            $this->jsonResponse([
                'vaga' => $vaga,
                'candidatos' => $candidatos
            ]);

        } catch (PDOException $e) {
            $this->jsonResponse(['erro' => 'Erro ao consultar o banco de dados.'], 500);
        }
    }
}