<?php

namespace App\Model;

use PDO;
use App\Core\Model;


class GestaoVagasModel extends Model{

    protected string $table = 'vaga'; // Nome da tabela no banco de dados
    //construtor tem na classe model

    //listar todas as vagas para serem exibindas na gestao de vagas
    public function listarVagas(): array{

        $query = "SELECT 
        v.titulo_vaga AS titulo,
        s.nome_setor AS departamento,
        v.situacao
        FROM 
            vaga AS v
        LEFT JOIN 
            setor AS s ON v.id_setor = s.id_setor
         ORDER BY
            v.titulo_vaga ASC";
        $stmt = $this->db_connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
