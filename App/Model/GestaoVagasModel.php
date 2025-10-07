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
        v.id_vaga,
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
    public function listarVagasAbertas(): array
    {
        $sql = "SELECT 
                    v.id_vaga,
                    v.titulo_vaga AS titulo,
                    v.descricao_vaga,
                    v.requisitos_necessarios AS requisitos,
                    v.data_criacao,
                    v.situacao,
                    s.nome_setor AS departamento
                FROM 
                    vaga AS v
                LEFT JOIN 
                    setor AS s ON v.id_setor = s.id_setor
                WHERE
                    v.situacao = :situacao
                ORDER BY
                    v.data_criacao DESC";

        $stmt = $this->db_connection->prepare($sql);

        $stmt->execute([':situacao' => 'aberta']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id)
    {
        $sql = "SELECT 
                    v.id_vaga,
                    v.titulo_vaga,
                    v.descricao_vaga,
                    s.nome_setor
                FROM 
                    vaga AS v
                LEFT JOIN 
                    setor AS s ON v.id_setor = s.id_setor
                WHERE 
                    v.id_vaga = :id_vaga
                LIMIT 1";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id_vaga' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
