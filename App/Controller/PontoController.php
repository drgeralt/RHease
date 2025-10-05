<?php

namespace App\Controller;

use App\Model\PontoModel;
use Exception;

class PontoController
{
    /**
     * Carrega e exibe a página principal de registo de ponto.
     */
    public function index()
    {
        $pontoModel = new PontoModel();
        $idColaborador = $_SESSION['id_colaborador'] ?? 1; // Pega o ID do colaborador

        $dataAtual = date('Y-m-d');
        $ultimoPonto = $pontoModel->getUltimoPontoAberto($idColaborador, $dataAtual);

        $horaEntrada = null;
        if ($ultimoPonto) {
            // Converte a data completa (ex: 2025-10-04 09:00:00) para apenas a hora (ex: 09:00)
            $horaEntrada = date('H:i', strtotime($ultimoPonto['data_hora_entrada']));
        }

        // Carrega a view. A variável $horaEntrada estará disponível dentro dela.
        require_once BASE_PATH . '/App/View/Colaborador/registroPonto.php';
    }

    /**
     * Recebe os dados do front-end (foto), guarda a imagem e
     * chama o modelo para registar o ponto na base de dados.
     */
    public function registrar()
    {
        header('Content-Type: application/json');
        $caminhoCompleto = null; // Variável para guardar o caminho do ficheiro

        try {
            // Valida se a imagem foi recebida
            if (!isset($_POST['imagem']) || empty($_POST['imagem'])) {
                throw new Exception('Nenhuma imagem recebida.');
            }

            // Processa a imagem recebida em formato Base64
            $imgData = $_POST['imagem'];
            @list($type, $imgData) = explode(';', $imgData);
            @list(, $imgData) = explode(',', $imgData);
            if (!$imgData) {
                throw new Exception('Formato de imagem inválido.');
            }
            $imgData = base64_decode($imgData);

            // Define as variáveis de tempo e o ID do colaborador (usando 1 como padrão)
            $idColaborador = $_SESSION['id_colaborador'] ?? 1;
            $timestamp = time();
            $dataHoraAtual = date('Y-m-d H:i:s', $timestamp);

            // Cria um nome de ficheiro único para a imagem
            $nomeArquivo = $idColaborador . '_' . $timestamp . '.jpg';
            $caminhoCompleto = BASE_PATH . '/storage/fotos_ponto/' . $nomeArquivo;

            // Guarda a imagem no disco do servidor
            if (file_put_contents($caminhoCompleto, $imgData) === false) {
                throw new Exception('Falha ao guardar a imagem no servidor. Verifique as permissões da pasta storage.');
            }

            // Instancia o modelo e chama o método que contém a lógica da base de dados
            $pontoModel = new PontoModel();
            $tipoDeRegisto = $pontoModel->registrarPonto($idColaborador, $dataHoraAtual);

            // Envia uma resposta de sucesso para o front-end
            echo json_encode([
                'status' => 'success',
                'message' => 'Ponto registado como ' . $tipoDeRegisto . ' com sucesso!',
                'horario' => date('H:i', $timestamp),
                'tipo' => $tipoDeRegisto
            ]);

        } catch (Exception $e) {
            // Se qualquer erro acontecer, apaga a foto que acabou de ser guardada (se existir)
            if ($caminhoCompleto && file_exists($caminhoCompleto)) {
                unlink($caminhoCompleto);
            }

            // Envia uma resposta de erro para o front-end com a mensagem específica
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }
}