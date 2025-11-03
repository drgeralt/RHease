<?php

namespace App\Model;

use PDO;
use App\Core\Model;
use PDOStatement;
use DateTime;

class ColaboradorModel extends Model
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // ... (métodos existentes omitidos para brevidade)
    public function getAll(): array
    {
        $sql = "SELECT 
                    c.id_colaborador, 
                    c.nome_completo, 
                    c.email_pessoal, 
                    c.cpf, 
                    c.data_nascimento, 
                    ca.nome_cargo as cargo, 
                    s.nome_setor as setor, 
                    c.situacao 
                FROM colaborador c
                LEFT JOIN cargo ca ON c.id_cargo = ca.id_cargo
                LEFT JOIN setor s ON c.id_setor = s.id_setor";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function listarColaboradores(): array
    {
        $sql = "SELECT 
                    c.id_colaborador,
                    c.nome_completo,
                    c.email_pessoal AS email,
                    c.telefone,
                    c.data_admissao,
                    c.situacao,
                    cargo.nome_cargo AS cargo,
                    setor.nome_setor AS departamento
                FROM 
                    colaborador AS c
                LEFT JOIN 
                    cargo ON c.id_cargo = cargo.id_cargo
                LEFT JOIN 
                    setor ON c.id_setor = setor.id_setor
                ORDER BY
                    c.nome_completo ASC";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function salarioPorID(int $id): float
    {
        $sql = "SELECT ca.salario_base FROM colaborador c
                JOIN cargo ca ON c.id_cargo = ca.id_cargo
                WHERE c.id_colaborador = :id";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $salario = $stmt->fetchColumn();

        return $salario ? (float) $salario : 0.0;
    }

    public function create(array $data): bool
    {
        $query = "INSERT INTO colaborador (
                      matricula, nome_completo, data_nascimento, cpf, rg, genero, email_pessoal, email_profissional, telefone,
                      data_admissao, tipo_contrato, situacao, id_cargo, id_setor, id_endereco
                  ) VALUES (
                      :matricula, :nome_completo, :data_nascimento, :cpf, :rg, :genero, :email_pessoal, :email_profissional, :telefone,
                      :data_admissao, :tipo_contrato, :situacao, :id_cargo, :id_setor, :id_endereco
                  )";

        $stmt = $this->db_connection->prepare($query);

        $stmt->bindValue(':matricula', $data['matricula']);
        $stmt->bindValue(':nome_completo', $data['nome_completo']);
        $stmt->bindValue(':data_nascimento', $data['data_nascimento']);
        $stmt->bindValue(':cpf', $data['cpf']);
        $stmt->bindValue(':rg', $data['rg']);
        $stmt->bindValue(':genero', $data['genero']);
        $stmt->bindValue(':email_pessoal', $data['email_pessoal']);
        $stmt->bindValue(':email_profissional', $data['email_profissional']);
        $stmt->bindValue(':telefone', $data['telefone']);
        $stmt->bindValue(':data_admissao', $data['data_admissao']);
        $stmt->bindValue(':tipo_contrato', $data['tipo_contrato']);
        $stmt->bindValue(':situacao', $data['situacao']);
        $stmt->bindValue(':id_cargo', $data['id_cargo'], PDO::PARAM_INT);
        $stmt->bindValue(':id_setor', $data['id_setor'], PDO::PARAM_INT);
        $stmt->bindValue(':id_endereco', $data['id_endereco'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function buscarPorId(int $id)
    {
        $sql = "SELECT 
                    col.id_colaborador, col.nome_completo, col.cpf AS CPF, col.rg AS RG, col.data_nascimento, col.genero, col.email_pessoal AS email, col.telefone, col.matricula, col.data_admissao, col.situacao,
                    end.CEP, end.logradouro, end.numero, end.bairro, end.cidade, end.estado,
                    car.nome_cargo, car.salario_base,
                    setr.nome_setor
                FROM 
                    colaborador AS col
                LEFT JOIN endereco AS end ON col.id_endereco = end.id_endereco
                LEFT JOIN cargo AS car ON col.id_cargo = car.id_cargo
                LEFT JOIN setor AS setr ON col.id_setor = setr.id_setor
                WHERE 
                    col.id_colaborador = :id
                LIMIT 1";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        $colaborador = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$colaborador) {
            return false;
        }

        return [
            'colaborador' => $colaborador,
            'endereco' => $colaborador,
            'cargo' => $colaborador,
            'setor' => $colaborador,
        ];
    }

    public function atualizarColaborador(array $dados): bool
    {
        try {
            $this->db_connection->beginTransaction();

            $stmt = $this->db_connection->prepare("SELECT id_cargo, id_setor, id_endereco FROM colaborador WHERE id_colaborador = :id");
            $stmt->execute([':id' => $dados['id_colaborador']]);
            $ids = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->db_connection->prepare("UPDATE cargo SET nome_cargo = :nome, salario_base = :salario WHERE id_cargo = :id");
            $stmt->execute([
                ':nome' => $dados['cargo']['nome_cargo'],
                ':salario' => $dados['cargo']['salario_base'],
                ':id' => $ids['id_cargo']
            ]);

            $stmt = $this->db_connection->prepare("UPDATE setor SET nome_setor = :nome WHERE id_setor = :id");
            $stmt->execute([':nome' => $dados['setor']['nome_setor'], ':id' => $ids['id_setor']]);

            $stmt = $this->db_connection->prepare(
                "UPDATE endereco SET CEP = :cep, logradouro = :log, numero = :num, bairro = :bairro, cidade = :cid, estado = :est WHERE id_endereco = :id"
            );
            $stmt->execute([
                ':cep' => $dados['endereco']['CEP'],
                ':log' => $dados['endereco']['logradouro'],
                ':num' => $dados['endereco']['numero'],
                ':bairro' => $dados['endereco']['bairro'],
                ':cid' => $dados['endereco']['cidade'],
                ':est' => $dados['endereco']['estado'],
                ':id' => $ids['id_endereco']
            ]);

            $stmt = $this->db_connection->prepare(
                "UPDATE colaborador SET nome_completo = :nome, data_nascimento = :dn, genero = :gen, email_pessoal = :email, telefone = :tel, situacao = :sit, data_admissao = :da WHERE id_colaborador = :id"
            );
            $stmt->execute([
                ':nome' => $dados['nome_completo'],
                ':dn' => $dados['data_nascimento'],
                ':gen' => $dados['genero'],
                ':email' => $dados['email_pessoal'],
                ':tel' => $dados['telefone'],
                ':sit' => $dados['situacao'],
                ':da' => $dados['data_admissao'],
                ':id' => $dados['id_colaborador']
            ]);

            $this->db_connection->commit();
            return true;

        } catch (\PDOException $e) {
            $this->db_connection->rollBack();
            error_log("Erro ao atualizar colaborador: " . $e->getMessage());
            return false;
        }
    }

    public function softDelete($id): void
    {
        $query = "UPDATE colaborador SET situacao = 'Inativo' WHERE id_colaborador = :id";
        $stmt = $this->db_connection->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }
    public function toggleStatus(int $id): bool
    {
        $stmt = $this->db_connection->prepare("SELECT situacao FROM colaborador WHERE id_colaborador = :id");
        $stmt->execute([':id' => $id]);
        $statusAtual = $stmt->fetchColumn();

        if (!$statusAtual) {
            return false;
        }

        $novoStatus = ($statusAtual === 'ativo') ? 'inativo' : 'ativo';

        $stmt = $this->db_connection->prepare("UPDATE colaborador SET situacao = :novoStatus WHERE id_colaborador = :id");

        return $stmt->execute([
            ':novoStatus' => $novoStatus,
            ':id' => $id
        ]);
    }

    public function binds(false|PDOStatement $stmt, $data): void
    {
        $stmt->bindValue(':nome_completo', $data['nome_completo']);
        $stmt->bindValue(':cpf', $data['cpf']);
        $stmt->bindValue(':rg', $data['rg']);
        $stmt->bindValue(':data_nascimento', $data['data_nascimento']);
        $stmt->bindValue(':genero', $data['genero']);
        $stmt->bindValue(':email_pessoal', $data['email_pessoal']);
        $stmt->bindValue(':telefone_celular', $data['telefone_celular']);
        $stmt->bindValue(':logradouro', $data['logradouro']);
        $stmt->bindValue(':numero', $data['numero']);
        $stmt->bindValue(':complemento', $data['complemento']);
        $stmt->bindValue(':bairro', $data['bairro']);
        $stmt->bindValue(':cidade', $data['cidade']);
        $stmt->bindValue(':estado', $data['estado']);
        $stmt->bindValue(':cep', $data['cep']);
        $stmt->bindValue(':cargo', $data['cargo']);
        $stmt->bindValue(':departamento', $data['departamento']);
        $stmt->bindValue(':salario', $data['salario']);
        $stmt->bindValue(':data_admissao', $data['data_admissao']);
        $stmt->bindValue(':data_desligamento', $data['data_desligamento']);
        $stmt->bindValue(':tipo_contrato', $data['tipo_contrato']);
        $stmt->bindValue(':email_corporativo', $data['email_corporativo']);
        $stmt->bindValue(':status', $data['status']);
    }

    public function findById(int $id)
    {
        $sql = "SELECT * FROM colaborador WHERE id_colaborador = :id";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDadosColaborador(int $userId): ?array {
        $sql = "
            SELECT 
                c.nome_completo, 
                c.email_profissional, 
                c.matricula,
                ca.nome_cargo,
                ca.salario_base, 
                s.nome_setor
            FROM 
                colaborador c
            LEFT JOIN 
                cargo ca ON c.id_cargo = ca.id_cargo
            LEFT JOIN 
                setor s ON c.id_setor = s.id_setor
            WHERE 
                c.id_colaborador = :id
        ";
        
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        return $result ?: null;
    }

    public function getUltimoPonto(int $userId): ?array
    {
        $sql = "SELECT data_hora_entrada, data_hora_saida FROM folha_ponto WHERE id_colaborador = :id ORDER BY data_hora_entrada DESC LIMIT 1";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getBeneficiosAtivosCount(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM colaborador_beneficio WHERE id_colaborador = :id";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function getUltimoSalarioLiquido(int $userId): ?float
    {
        $sql = "SELECT salario_liquido FROM holerites WHERE id_colaborador = :id ORDER BY ano_referencia DESC, mes_referencia DESC LIMIT 1";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetchColumn();
        return $result ? (float) $result : null;
    }

    public function getHorasTrabalhadasSemana(int $userId): float
    {
        $sql = "
            SELECT SUM(TIMESTAMPDIFF(HOUR, data_hora_entrada, data_hora_saida))
            FROM folha_ponto
            WHERE id_colaborador = :id AND WEEK(data_hora_entrada, 1) = WEEK(CURDATE(), 1)
        ";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return (float) $stmt->fetchColumn();
    }

    public function getDadosGraficoSalario(int $userId): array
    {
        $sql = "
            SELECT 
                h.total_proventos, 
                h.total_descontos, 
                h.salario_liquido
            FROM holerites h
            WHERE h.id_colaborador = :id
            ORDER BY h.ano_referencia DESC, h.mes_referencia DESC
            LIMIT 1
        ";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_proventos' => 0,
            'total_descontos' => 0,
            'salario_liquido' => 0
        ];
    }

    public function getDadosGraficoHoras(int $userId): array
    {
        $sql = "
            SELECT 
                DAYOFWEEK(data_hora_entrada) as dia_semana,
                SUM(TIMESTAMPDIFF(MINUTE, data_hora_entrada, data_hora_saida)) / 60 as horas
            FROM folha_ponto
            WHERE id_colaborador = :id AND WEEK(data_hora_entrada, 1) = WEEK(CURDATE(), 1)
            GROUP BY dia_semana
        ";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    public function getTotalBeneficiosValor($idColaborador) {
    $sql = "SELECT SUM(valor_especifico) AS total 
            FROM colaborador_beneficio 
            WHERE id_colaborador = :id 
              AND valor_especifico IS NOT NULL";
    
    $stmt = $this->db_connection->prepare($sql);
    $stmt->bindValue(':id', $idColaborador, \PDO::PARAM_INT);
    $stmt->execute();
    
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $row['total'] ?? 0;
}
    public function getTotalDescontos($id_colaborador) {
    // Pega mês e ano atuais
    $mes_atual = date('m');
    $ano_atual = date('Y');

    $sql = "SELECT total_descontos 
            FROM holerites 
            WHERE id_colaborador = :id_colaborador
              AND mes_referencia = :mes
              AND ano_referencia = :ano
            LIMIT 1";

    $stmt = $this->db_connection->prepare($sql);
    $stmt->bindValue(':id_colaborador', $id_colaborador, PDO::PARAM_INT);
    $stmt->bindValue(':mes', $mes_atual, PDO::PARAM_INT);
    $stmt->bindValue(':ano', $ano_atual, PDO::PARAM_INT);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultado ? (float)$resultado['total_descontos'] : 0;
}



    /**
     * Busca os itens detalhados (proventos e descontos) do último holerite de um colaborador.
     * @param int $userId
     * @return array Retorna um array de itens do holerite ou um array vazio.
     */
    public function getItensUltimoHolerite(int $userId): array
    {
        // Primeiro, encontrar o ID do último holerite
        $sql_ultimo_holerite = "SELECT id_holerite FROM holerites WHERE id_colaborador = :id ORDER BY ano_referencia DESC, mes_referencia DESC LIMIT 1";
        $stmt_ultimo = $this->db_connection->prepare($sql_ultimo_holerite);
        $stmt_ultimo->execute([':id' => $userId]);
        $id_holerite = $stmt_ultimo->fetchColumn();

        if (!$id_holerite) {
            return []; // Retorna vazio se não houver holerite
        }

        // CORREÇÃO: Adicionado `descricao` ao SELECT.
        $sql_itens = "SELECT descricao, tipo, valor FROM holerite_itens WHERE id_holerite = :id_holerite";
        $stmt_itens = $this->db_connection->prepare($sql_itens);
        $stmt_itens->execute([':id_holerite' => $id_holerite]);
        
        return $stmt_itens->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}