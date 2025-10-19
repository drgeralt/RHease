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
        $idColaborador = $_SESSION['id_colaborador'] ?? 1; // MODIFICAR

        $dataAtual = date('Y-m-d');
        $ultimoPonto = $pontoModel->getUltimoPontoAberto($idColaborador, $dataAtual);

        $horaEntrada = null;
        if ($ultimoPonto) {
            $horaEntrada = date('H:i', strtotime($ultimoPonto['data_hora_entrada']));
        }

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

            $idColaborador = $_SESSION['id_colaborador'] ?? 1;
            $timestamp = time();
            $dataHoraAtual = date('Y-m-d H:i:s', $timestamp);
            $geolocalizacao = $_POST['geolocalizacao'] ?? 'Não informada';
            $nomeArquivo = $idColaborador . '_' . $timestamp . '.jpg';
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

            $pontoModel = new PontoModel();
            $tipoDeRegisto = $pontoModel->registrarPonto($idColaborador, $dataHoraAtual, $geolocalizacao,
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
