<?php
// Headers obrigatórios
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

// Verifica se o método da requisição é GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

// Define o caminho base do projeto
define('BASE_PATH', realpath(__DIR__ . '/../../'));

// Inclui o Model
require_once BASE_PATH . '/app/Model/Colaborador.php';

// Instancia o objeto Colaborador
$colaborador = new Colaborador();

// Obtém o ID da URL (se houver)
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($id) {
    // Busca um único colaborador pelo ID (incluindo inativos)
    $resultado = $colaborador->read($id);
    if ($resultado) {
        http_response_code(200); // OK
        echo json_encode($resultado);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["message" => "Colaborador não encontrado."]);
    }
} else {
    // Busca a lista de todos os colaboradores ATIVOS
    $resultado = $colaborador->read();
    http_response_code(200); // OK
    echo json_encode($resultado ?: []); // Retorna os resultados ou um array vazio
}