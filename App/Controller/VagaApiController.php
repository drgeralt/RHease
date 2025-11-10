<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use PDOException;

class VagaApiController extends Controller
{
    // [API] GET /api/vagas/listar 
    public function listarVagas(): void 
    {
        try {
            $vagaModel = $this->model('GestaoVagas');
            $vagas = $vagaModel->listarVagas();
            
            // Usa o método padronizado da classe pai
            $this->jsonResponse(true, 'Vagas carregadas com sucesso', $vagas);
            
        } catch (PDOException $e) {
            // Log do erro para debug
            error_log("ERRO NO BANCO - listarVagas: " . $e->getMessage());
            error_log("Código do erro: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $this->jsonResponse(
                false, 
                'Erro ao consultar o banco de dados.', 
                ['detalhes' => $e->getMessage(), 'codigo' => $e->getCode()], 
                500
            );
        } catch (\Exception $e) {
            error_log("ERRO GERAL - listarVagas: " . $e->getMessage());
            $this->jsonResponse(
                false, 
                'Erro ao processar requisição.', 
                ['detalhes' => $e->getMessage()], 
                500
            );
        }
    }

    // [API] POST /api/vagas/salvar 
    public function salvar(): void
    {
        $dados = json_decode(file_get_contents('php://input'), true);
        
        $erros = [];
        $titulo = trim($dados['titulo'] ?? '');
        $departamento = trim($dados['departamento'] ?? '');

        if (empty($titulo) || empty($departamento)) {
            $erros['geral'] = 'Título e Departamento são obrigatórios.';
        }

        if (!empty($erros)) {
            $this->jsonResponse(false, 'Dados inválidos', ['erros' => $erros], 422);
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

            $this->jsonResponse(true, 'Vaga criada com sucesso', ['id_vaga' => $novoId], 201);

        } catch (PDOException $e) {
            $this->jsonResponse(false, 'Erro ao salvar a vaga no banco.', null, 500);
        }
    }

    // [API] GET /api/vagas/editar?id=...
    public function editar(): void
    {
        $idVaga = (int)($_GET['id'] ?? 0);
        if ($idVaga === 0) {
            $this->jsonResponse(false, 'ID da vaga não fornecido.', null, 400);
            return;
        }

        try {
            $vagaModel = $this->model('GestaoVagas');
            $vaga = $vagaModel->buscarPorId($idVaga);
            if (!$vaga) {
                $this->jsonResponse(false, 'Vaga não encontrada.', null, 404);
                return;
            }
            $this->jsonResponse(true, 'Vaga encontrada', $vaga);
        } catch (PDOException $e) {
            $this->jsonResponse(false, 'Erro ao consultar o banco de dados.', null, 500);
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
            $this->jsonResponse(false, 'Dados inválidos', null, 422);
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

            $this->jsonResponse(true, 'Vaga atualizada com sucesso');
        } catch (PDOException $e) {
            $this->jsonResponse(false, 'Erro ao atualizar a vaga no banco.', null, 500);
        }
    }

    // [API] GET /api/vagas/excluir?id=...
    public function excluir(): void
    {
        $idVaga = (int)($_GET['id'] ?? 0);
        if ($idVaga === 0) {
            $this->jsonResponse(false, 'ID da vaga não fornecido.', null, 400);
            return;
        }
        try {
            $vagaModel = $this->model('GestaoVagas');
            $vagaModel->excluirVaga($idVaga);
            $this->jsonResponse(true, 'Vaga excluída com sucesso');
        } catch (PDOException $e) {
            $this->jsonResponse(false, 'Erro ao excluir a vaga.', null, 500);
        }
    }

    // [API] GET /api/vagas/candidatos?id=... 
    public function verCandidatos()
    {
        $idVaga = (int)($_GET['id'] ?? 0); 
        if ($idVaga === 0) {
            $this->jsonResponse(false, 'ID da vaga não fornecido.', null, 400);
            return;
        }

        try {
            $vagaModel = $this->model('GestaoVagas');
            $candidaturaModel = $this->model('Candidatura');

            $vaga = $vagaModel->buscarPorId($idVaga);
            $candidatos = $candidaturaModel->buscarPorVaga($idVaga);

            if (!$vaga) {
                $this->jsonResponse(false, 'Vaga não encontrada.', null, 404);
                return;
            }
            
            $this->jsonResponse(true, 'Candidatos encontrados', [
                'vaga' => $vaga,
                'candidatos' => $candidatos
            ]);

        } catch (PDOException $e) {
            $this->jsonResponse(false, 'Erro ao consultar o banco de dados.', null, 500);
        }
    }
}