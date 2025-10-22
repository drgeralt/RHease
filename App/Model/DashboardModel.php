<?php
namespace App\Model;

use PDO;
use PDOException;

class DashboardModel {
    private $pdo;

    public function __construct() {
        // Assume a mesma conexão PDO que os outros models
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=rhease;charset=utf8", 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \Exception("Falha na Conexão com o Banco de Dados.");
        }
    }

    /**
     * Busca todos os dados agregados para o dashboard do gestor.
     * @return array
     */
    public function getGestorDashboardData(): array {
        $dados = [];

        // 1. Total de Colaboradores Ativos
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM colaborador WHERE situacao = 'ativo'");
        $dados['total_colaboradores_ativos'] = $stmt->fetchColumn();

        // 2. Total de Vagas Publicadas (Abertas)
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM vaga WHERE situacao = 'aberta'");
        $dados['total_vagas_publicadas'] = $stmt->fetchColumn();

        // 3. Total de Currículos/Candidaturas
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM candidaturas");
        $dados['total_curriculos_processados'] = $stmt->fetchColumn();

        // 4. Total de Benefícios Ativos no Catálogo
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM beneficios_catalogo WHERE status = 'Ativo'");
        $dados['total_beneficios_ativos'] = $stmt->fetchColumn();

        // 5. Distribuição de Tipos de Contrato
        $stmt = $this->pdo->query("SELECT tipo_contrato, COUNT(*) as total FROM colaborador GROUP BY tipo_contrato");
        $dados['distribuicao_contratos'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return $dados;
    }

    /**
     * Busca o tipo de usuário (role) de um colaborador logado.
     * @param int $userId
     * @return string|null
     */
    public function getUserRole(int $userId): ?string {
        // Supondo que a tabela 'colaborador' tenha uma coluna 'tipo_usuario' ('gestor' ou 'colaborador')
        $stmt = $this->pdo->prepare("SELECT perfil FROM colaborador WHERE id_colaborador = :id");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchColumn() ?: 'colaborador'; // Retorna 'colaborador' como padrão
    }
}
