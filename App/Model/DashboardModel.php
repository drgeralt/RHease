<?php
declare(strict_types=1);

namespace App\Model;

use App\Core\Model;
use PDO;

class DashboardModel extends Model
{
    public function getGestorDashboardData(): array
    {
        $dados = [];

        $stmt = $this->db_connection->query("SELECT COUNT(*) FROM colaborador WHERE situacao = 'ativo'");
        $dados['total_colaboradores_ativos'] = $stmt->fetchColumn();

        $stmt = $this->db_connection->query("SELECT COUNT(*) FROM vaga WHERE situacao = 'aberta'");
        $dados['total_vagas_publicadas'] = $stmt->fetchColumn();

        $stmt = $this->db_connection->query("SELECT COUNT(*) FROM candidaturas");
        $dados['total_curriculos_processados'] = $stmt->fetchColumn();

        $stmt = $this->db_connection->query("SELECT COUNT(*) FROM beneficios_catalogo WHERE status = 'Ativo'");
        $dados['total_beneficios_ativos'] = $stmt->fetchColumn();

        $stmt = $this->db_connection->query("SELECT tipo_contrato, COUNT(*) as total FROM colaborador WHERE situacao = 'ativo' GROUP BY tipo_contrato");
        $dados['distribuicao_contratos'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return $dados;
    }

    public function getUserRole(int $userId): ?string
    {
        $stmt = $this->db_connection->prepare("SELECT perfil FROM colaborador WHERE id_colaborador = :id");
        $stmt->execute([':id' => $userId]);
        $perfil = $stmt->fetchColumn();

        return $perfil ?: 'colaborador';
    }
}