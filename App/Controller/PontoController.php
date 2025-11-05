<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\ColaboradorModel;
use App\Model\PontoModel;
use Exception;

class PontoController
{
    /**
     * Carrega e exibe a página principal de registo de ponto.
     */
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $pdo = Database::getInstance();
        // CORREÇÃO: Passando a conexão PDO para o construtor do PontoModel.
        $pontoModel = new PontoModel($pdo);
        $colaboradorModel = new ColaboradorModel($pdo);

        // Busca os dados do colaborador para a view
        $colaborador = $colaboradorModel->getDadosColaborador($userId);

        $dataAtual = date('Y-m-d');
        $ultimoPonto = $pontoModel->getUltimoPontoAberto($userId, $dataAtual);

        $horaEntrada = null;
        if ($ultimoPonto) {
            $horaEntrada = date('H:i', strtotime($ultimoPonto['data_hora_entrada']));
        }

        // A variável $colaborador estará disponível na view
        require_once BASE_PATH . '/App/View/Colaborador/registroPonto.php';
    }

    /**
     * Recebe os dados do front-end (foto), guarda a imagem e
     * chama o modelo para registar o ponto na base de dados.
     */
    public function registrar()
    {
        header('Content-Type: application/json');
        $caminhoCompleto = null;

        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                throw new Exception('Usuário não autenticado. Faça login novamente.');
            }

            if (!isset($_POST['imagem']) || empty($_POST['imagem'])) {
                throw new Exception('Nenhuma imagem recebida.');
            }

            $imgData = $_POST['imagem'];
            @list($type, $imgData) = explode(';', $imgData);
            @list(, $imgData) = explode(',', $imgData);
            if (!$imgData) {
                throw new Exception('Formato de imagem inválido.');
            }
            $imgData = base64_decode($imgData);

            $timestamp = time();
            $dataHoraAtual = date('Y-m-d H:i:s', $timestamp);
            $geolocalizacao = $_POST['geolocalizacao'] ?? 'Não informada';
            $nomeArquivo = $userId . '_' . $timestamp . '.jpg';
            $caminhoRelativo = 'storage/fotos_ponto/' . $nomeArquivo;
            $caminhoCompleto = BASE_PATH . '/' . $caminhoRelativo;
            $diretorioFotos = dirname($caminhoCompleto);
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? "Não identificado";

            if (!is_dir($diretorioFotos)) {
                if (!mkdir($diretorioFotos, 0775, true)) {
                    throw new Exception("Falha ao criar diretório 'RHease/storage/fotos_ponto'!");
                };
            }

            if (file_put_contents($caminhoCompleto, $imgData) === false) {
                throw new Exception('Falha ao guardar a imagem no servidor. Verifique as permissões da pasta /storage.');
            }

            $pdo = Database::getInstance();
            // CORREÇÃO: Passando a conexão PDO para o construtor do PontoModel.
            $pontoModel = new PontoModel($pdo);
            $tipoDeRegisto = $pontoModel->registrarPonto($userId, $dataHoraAtual, $geolocalizacao,
                $caminhoRelativo, $ipAddress);

            echo json_encode([
                'status' => 'success',
                'message' => 'Ponto registado como ' . $tipoDeRegisto . ' com sucesso!',
                'horario' => date('H:i', $timestamp),
                'tipo' => $tipoDeRegisto
            ]);

        } catch (Exception $e) {
            if ($caminhoCompleto && file_exists($caminhoCompleto)) {
                unlink($caminhoCompleto);
            }

            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }
}
