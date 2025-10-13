<?php
// Headers obrigatórios
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Verifica se o método da requisição é PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

// Obtém o ID da URL e os dados do corpo da requisição
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$data = json_decode(file_get_contents("php://input"), true);

// Validação dos dados
if (!$id) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "ID do colaborador não fornecido na URL."]);
    exit();
}

if (
    empty($data['nome_completo']) ||
    empty($data['CPF']) ||
    empty($data['RG']) ||
    empty($data['email']) ||
    empty($data['data_admissao']) ||
    empty($data['id_setor'])
) {
    http_response_code(422); // Unprocessable Entity
    echo json_encode(["message" => "Não foi possível atualizar. Dados obrigatórios incompletos."]);
    exit();
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(["message" => "Formato de e-mail inválido."]);
    exit();
}

// Tenta atualizar
if ($colaborador->update($id, $data)) {
    http_response_code(200); // OK
    echo json_encode(["message" => "Colaborador atualizado com sucesso."]);
} else {
    http_response_code(503); // Service Unavailable
    echo json_encode(["message" => "Não foi possível atualizar o colaborador."]);
}