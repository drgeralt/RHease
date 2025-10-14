<?php

namespace App\Core;

use PDO;

class Model
{
    /** @var PDO A conexão com o banco de dados */
    protected PDO $db_connection;

    /**
     * O construtor recebe a conexão PDO para que todos os models filhos a utilizem.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->db_connection = $pdo;
    }
}