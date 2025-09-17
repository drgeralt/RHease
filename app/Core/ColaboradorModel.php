<?php

namespace App\Models; // Namespace correto para a pasta app/Models

use PDO;

class ColaboradorModel {
    //Esta classe não deve conter lógica de negócio, apenas os métodos para interagir com o banco de dados.
    private $conn;
    private $table = 'colaboradores';

    public $id;
    public $nome;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }
}
