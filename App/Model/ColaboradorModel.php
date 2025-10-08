<?php

namespace App\Model;

use PDO;
use App\Core\Model;
use PDOStatement;

class ColaboradorModel extends Model
{
    protected PDO $db_connection;

    public function __construct(PDO $pdo)
    {
        $this->db_connection = $pdo;
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


    public function create(array $data): void
    {
        $query = "INSERT INTO colaborador (
                      matricula, nome_completo, data_nascimento, CPF, RG, genero, email, telefone, 
                      data_admissao, situacao, id_cargo, id_setor, id_endereco
                  ) VALUES (
                      :matricula, :nome_completo, :data_nascimento, :CPF, :RG, :genero, :email, :telefone,
                      :data_admissao, :situacao, :id_cargo, :id_setor, :id_endereco
                  )";

        $stmt = $this->db_connection->prepare($query);

        // Bind dos valores
        $stmt->bindValue(':matricula', $data['matricula']);
        $stmt->bindValue(':nome_completo', $data['nome_completo']);
        $stmt->bindValue(':data_nascimento', $data['data_nascimento']);
        $stmt->bindValue(':CPF', $data['CPF']);
        $stmt->bindValue(':RG', $data['RG']);
        $stmt->bindValue(':genero', $data['genero']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':telefone', $data['telefone']);
        $stmt->bindValue(':data_admissao', $data['data_admissao']);
        $stmt->bindValue(':situacao', $data['situacao']);
        $stmt->bindValue(':id_cargo', $data['id_cargo']);
        $stmt->bindValue(':id_setor', $data['id_setor']);
        $stmt->bindValue(':id_endereco', $data['id_endereco']);

        $stmt->execute();
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
        // Para uma atualização robusta, o ideal seria ter models para Cargo, Setor e Endereço
        // com métodos findOrCreateByName(), mas por simplicidade, faremos a lógica aqui.

        try {
            $this->db_connection->beginTransaction();

            // 1. Obter os IDs das tabelas relacionadas
            $stmt = $this->db_connection->prepare("SELECT id_cargo, id_setor, id_endereco FROM colaborador WHERE id_colaborador = :id");
            $stmt->execute([':id' => $dados['id_colaborador']]);
            $ids = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Atualizar a tabela 'cargo'
            $stmt = $this->db_connection->prepare("UPDATE cargo SET nome_cargo = :nome, salario_base = :salario WHERE id_cargo = :id");
            $stmt->execute([
                ':nome' => $dados['cargo']['nome_cargo'],
                ':salario' => $dados['cargo']['salario_base'],
                ':id' => $ids['id_cargo']
            ]);

            // 3. Atualizar a tabela 'setor'
            $stmt = $this->db_connection->prepare("UPDATE setor SET nome_setor = :nome WHERE id_setor = :id");
            $stmt->execute([':nome' => $dados['setor']['nome_setor'], ':id' => $ids['id_setor']]);

            // 4. Atualizar a tabela 'endereco'
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

            // 5. Atualizar a tabela principal 'colaborador'
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

            // Se tudo correu bem, confirma as alterações
            $this->db_connection->commit();
            return true;

        } catch (\PDOException $e) {
            // Se algo falhou, desfaz todas as alterações
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
        // 1. Descobre o status atual do colaborador
        $stmt = $this->db_connection->prepare("SELECT situacao FROM colaborador WHERE id_colaborador = :id");
        $stmt->execute([':id' => $id]);
        $statusAtual = $stmt->fetchColumn();

        if (!$statusAtual) {
            return false; // Retorna falso se o colaborador não for encontrado
        }

        // 2. Determina qual será o novo status
        $novoStatus = ($statusAtual === 'ativo') ? 'inativo' : 'ativo';

        // 3. Executa a atualização no banco de dados
        $stmt = $this->db_connection->prepare("UPDATE colaborador SET situacao = :novoStatus WHERE id_colaborador = :id");

        return $stmt->execute([
            ':novoStatus' => $novoStatus,
            ':id' => $id
        ]);
    }

    /**
     * @param false|PDOStatement $stmt
     * @param $data
     * @return void
     */
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
}