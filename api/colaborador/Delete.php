<?php

// Headers obrigatórios
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Verifica se o método da requisição é DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

// Define o caminho base do projeto
define('BASE_PATH', realpath(__DIR__ . '/../../'));

// Inclui o Model
require_once BASE_PATH . '/app/Model/Colaborador.php';

// Instancia o objeto
$colaborador = new Colaborador();

// Obtém o ID da URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Valida se o ID foi fornecido
if (!$id) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "ID do colaborador não fornecido."]);
    exit();
}

// Tenta desativar o colaborador (Soft Delete)
if ($colaborador->deactivate($id)) {
    http_response_code(200); // OK
    echo json_encode(["message" => "Colaborador desativado com sucesso."]);
} else {
    http_response_code(503); // Service Unavailable
    echo json_encode(["message" => "Não foi possível desativar o colaborador."]);
}