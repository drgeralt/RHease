<?php
define('BASE_PATH', realpath(__DIR__ . '/../../'));
// 1. Definir os headers HTTP corretos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

// Verifica se o método da requisição é GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

// 2. Incluir e 3. Instanciar
require_once BASE_PATH . 'app/Model/Colaborador.php';
$colaborador = new Colaborador();

// 4. Obter dados (neste caso, o ID da URL)
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// 6. Chamar o método apropriado
if ($id) {
    // Busca um único colaborador
    $resultado = $colaborador->read($id);
    if ($resultado) {
        http_response_code(200); // OK
        echo json_encode($resultado);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["message" => "Colaborador não encontrado."]);
    }
} else {
    // Busca todos os colaboradores
    $resultado = $colaborador->read();
    http_response_code(200); // OK
    echo json_encode($resultado ?: []); // Retorna os resultados ou um array vazio
}
