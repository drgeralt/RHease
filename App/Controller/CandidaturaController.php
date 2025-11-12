<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Model\CandidaturaModel;
use App\Model\GestaoVagasModel;
use App\Model\CandidatoModel;
use App\Service\Implementations\AnalisadorCurriculoService;
use Smalot\PdfParser\Parser;
use PDO;
use Exception; // Importa a classe Exception base

/**
 * Class CandidaturaController
 * * Gerencia todo o fluxo de candidatura, desde a listagem de vagas
 * até a aplicação e análise de currículos por IA.
 * Esta classe é refatorada para Injeção de Dependência,
 * tornando-a totalmente testável.
 */
class CandidaturaController extends Controller
{
    protected CandidaturaModel $candidaturaModel;
    protected GestaoVagasModel $gestaoVagasModel;
    protected CandidatoModel $candidatoModel;
    protected AnalisadorCurriculoService $analisadorService;
    protected Parser $pdfParser;

    /**
     * Construtor do Controller.
     * Recebe todas as suas dependências (Models e Services) via injeção.
     */
    public function __construct(
        CandidaturaModel $candidaturaModel,
        GestaoVagasModel $gestaoVagasModel,
        CandidatoModel $candidatoModel,
        AnalisadorCurriculoService $analisadorService,
        Parser $pdfParser,
        PDO $pdo
    ) {
        parent::__construct($pdo);
        $this->candidaturaModel = $candidaturaModel;
        $this->gestaoVagasModel = $gestaoVagasModel;
        $this->candidatoModel = $candidatoModel;
        $this->analisadorService = $analisadorService;
        $this->pdfParser = $pdfParser;
    }

    /**
     * Exibe a lista de vagas abertas para os candidatos.
     */
    public function listar()
    {
        // Usa o Model injetado
        $vagasAbertas = $this->gestaoVagasModel->listarVagasAbertas();

        return $this->view('Candidatura/lista_vagas', ['vagas' => $vagasAbertas]);
    }

    /**
     * Exibe o formulário de candidatura para uma vaga específica.
     */
    public function exibirFormulario()
    {
        $idVaga = (int)($_POST['id'] ?? 0);

        if ($idVaga === 0) {
            header('Location: ' . BASE_URL . '/vagas');
            exit;
        }

        // Usa o Model injetado
        $vaga = $this->gestaoVagasModel->buscarPorId($idVaga);

        if (!$vaga) {
            header('Location: ' . BASE_URL . '/vagas?error=not_found');
            exit;
        }

        return $this->view('Candidatura/formulario_candidatura', ['vaga' => $vaga]);
    }

    /**
     * Processa o formulário de candidatura, faz o upload do currículo
     * e salva os dados no banco de dados.
     */
    public function aplicar()
    {
        try {
            $idVaga = (int)($_POST['id_vaga'] ?? 0);
            $dadosCandidato = [
                'nome_completo' => $_POST['nome_completo'] ?? null,
                'cpf' => $_POST['cpf'] ?? null,
            ];
            $arquivoCurriculo = $_FILES['curriculo_pdf'] ?? null;

            if (!$idVaga || empty($dadosCandidato['nome_completo']) || empty($dadosCandidato['cpf']) || !$arquivoCurriculo || $arquivoCurriculo['error'] !== UPLOAD_ERR_OK) {
                header('Location: ' . BASE_URL . '/vagas?error=dados_invalidos');
                exit;
            }

            // A lógica de upload poderia ser movida para um Service de Upload,
            // mas por enquanto a mantemos aqui para simplicidade.
            $uploadDir = BASE_PATH . '/public/uploads/curriculos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $nomeArquivo = uniqid() . '-' . basename($arquivoCurriculo['name']);
            $caminhoCompleto = $uploadDir . $nomeArquivo;
            $caminhoRelativoParaBD = '/uploads/curriculos/' . $nomeArquivo;

            if (!move_uploaded_file($arquivoCurriculo['tmp_name'], $caminhoCompleto)) {
                header('Location: ' . BASE_URL . '/vagas?error=falha_upload');
                exit;
            }

            $dadosCandidato['curriculo'] = $caminhoRelativoParaBD;

            // Usa os Models injetados
            $idCandidato = $this->candidatoModel->buscarOuCriar($dadosCandidato);

            if ($this->candidaturaModel->verificarExistente($idVaga, $idCandidato)) {
                header('Location: ' . BASE_URL . '/vagas?aviso=candidatura_existente');
                exit;
            }

            $sucesso = $this->candidaturaModel->criar($idVaga, $idCandidato);

            if ($sucesso) {
                header('Location: ' . BASE_URL . '/vagas?sucesso=candidatura_enviada');
            } else {
                header('Location: ' . BASE_URL . '/vagas?error=erro_banco');
            }
            exit;

        } catch (Exception $e) {
            // Lidar com exceções (ex: logar o erro)
            header('Location: ' . BASE_URL . '/vagas?error=inesperado');
            exit;
        }
    }

    /**
     * Ponto de entrada para analisar um currículo.
     * Verifica se a análise já existe; se sim, exibe; se não, processa.
     */
    public function analisarCurriculo()
    {
        $idCandidatura = (int)($_POST['id_candidatura'] ?? 0);
        if ($idCandidatura === 0) {
            die("Erro: ID da candidatura não fornecido.");
        }

        // Usa o Model injetado
        $analise = $this->candidaturaModel->buscarAnaliseCompleta($idCandidatura);

        if ($analise && $analise['pontuacao_aderencia'] !== null) {
            return $this->view('Candidatura/resultado_ia', [
                'candidatura' => $analise
            ]);
        }

        try {
            $resultado = $this->processarCurriculoIA($idCandidatura);

            if ($resultado['sucesso']) {
                header('Location: ' . BASE_URL . '/candidatura/ver-analise?id=' . $idCandidatura);
                exit;
            } else {
                die("Erro no processamento da IA: " . $resultado['erro']);
            }
        } catch (Exception $e) {
            die("Ocorreu um erro: " . $e->getMessage());
        }
    }

    /**
     * Lógica privada para processar o currículo com a IA.
     * Esta lógica usa as dependências injetadas.
     */
    private function processarCurriculoIA(int $idCandidatura): array
    {
        // Usa o Model injetado
        $candidatura = $this->candidaturaModel->buscarComVaga($idCandidatura);

        if (!$candidatura || empty($candidatura['curriculo']) || empty($candidatura['descricao_vaga'])) {
            return ['sucesso' => false, 'erro' => 'Dados da candidatura ou vaga incompletos.'];
        }

        $caminhoRelativo = $candidatura['curriculo'];
        $caminhoCompleto = BASE_PATH . '/public' . $caminhoRelativo;

        if (!file_exists($caminhoCompleto)) {
            return ['sucesso' => false, 'erro' => 'Ficheiro do currículo não encontrado.'];
        }

        // Usa o Parser de PDF injetado
        $pdf = $this->pdfParser->parseFile($caminhoCompleto);
        $textoDoCurriculo = $pdf->getText();

        // Usa o Serviço de Análise injetado
        $resultadoAnalise = $this->analisadorService->analisar($textoDoCurriculo, $candidatura);

        if ($resultadoAnalise['sucesso']) {
            $sumario = $resultadoAnalise['resultado']['sumario'] ?? 'Sumário não retornado.';
            $nota = (int)($resultadoAnalise['resultado']['nota'] ?? 0);

            // Usa o Model injetado para salvar
            $sucessoUpdate = $this->candidaturaModel->atualizarResultadoIA($idCandidatura, $nota, $sumario);

            if (!$sucessoUpdate) {
                return ['sucesso' => false, 'erro' => 'Falha ao salvar os resultados da IA no banco de dados.'];
            }
            return ['sucesso' => true];

        } else {
            return ['sucesso' => false, 'erro' => 'Erro na análise de IA: ' . $resultadoAnalise['erro']];
        }
    }

    /**
     * Exibe o resultado de uma análise de IA já processada.
     */
    public function exibirAnaliseIA()
    {
        $idCandidatura = (int)($_GET['id'] ?? 0);

        if ($idCandidatura === 0) {
            header('Location: ' . BASE_URL . '/vagas/listar');
            exit;
        }

        // Usa o Model injetado
        $analise = $this->candidaturaModel->buscarAnaliseCompleta($idCandidatura);

        if (!$analise || $analise['pontuacao_aderencia'] === null) {
            header('Location: ' . BASE_URL . '/vagas/listar?error=analise_nao_encontrada');
            exit;
        }

        return $this->view('Candidatura/resultado_ia', ['candidatura' => $analise]);
    }

    /**
     * Redireciona o usuário para a página principal de listagem de vagas.
     */
    public function redirecionarParaVagas()
    {
        header('Location: ' . BASE_URL . '/vagas');
        exit;
    }
}