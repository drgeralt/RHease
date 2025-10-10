<?php
declare(strict_types=1);

namespace App\Model;

use App\Core\Model;
use PDO;

class CandidatoModel extends Model
{
    /**
     * Busca um candidato pelo CPF. Se não encontrar, cria um novo.
     * Atualiza o caminho do currículo se o candidato já existir.
     *
     * @param array $dados Os dados do candidato ['nome_completo', 'cpf', 'curriculo']
     * @return int O ID do candidato existente ou recém-criado.
     */
    public function buscarOuCriar(array $dados): int
    {
        // Verifica se o candidato já existe pelo CPF
        $sql = "SELECT id_candidato FROM candidato WHERE CPF = :cpf LIMIT 1";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':cpf' => $dados['cpf']]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            // Candidato encontrado, atualiza o currículo
            $idCandidato = (int)$resultado['id_candidato'];
            $updateSql = "UPDATE candidato SET curriculo = :curriculo, nome_completo = :nome WHERE id_candidato = :id";
            $updateStmt = $this->db_connection->prepare($updateSql);
            $updateStmt->execute([
                ':curriculo' => $dados['curriculo'],
                ':nome'      => $dados['nome_completo'],
                ':id'        => $idCandidato
            ]);
            return $idCandidato;
        } else {
            // Candidato não existe, cria um novo
            $insertSql = "INSERT INTO candidato (nome_completo, CPF, curriculo) VALUES (:nome, :cpf, :curriculo)";
            $insertStmt = $this->db_connection->prepare($insertSql);
            $insertStmt->execute([
                ':nome' => $dados['nome_completo'],
                ':cpf' => $dados['cpf'],
                ':curriculo' => $dados['curriculo']
            ]);
            return (int)$this->db_connection->lastInsertId();
        }
    }
}