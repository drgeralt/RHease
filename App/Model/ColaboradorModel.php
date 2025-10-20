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
    public function getAll(): array
    {
        $query = "SELECT 
            c.id_colaborador, 
            c.nome_completo,
            c.data_admissao,
            c.situacao,
            ca.nome_cargo AS cargo,
            s.nome_setor AS departamento
        FROM 
            colaborador AS c
        LEFT JOIN 
            cargo AS ca ON c.id_cargo = ca.id_cargo
        LEFT JOIN 
            setor AS s ON c.id_setor = s.id_setor
        WHERE
            c.situacao = 'ativo'
        ORDER BY
            c.nome_completo ASC";

        $stmt = $this->db_connection->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function salarioPorID(int $id): float
    {
        $sql = "SELECT salario_base FROM colaborador WHERE id_colaborador = :id";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $salario = $stmt->fetchColumn();
        return $salario ? (float) $salario : 0.0;
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

    public function getById($id){
        $query = "SELECT * FROM colaborador WHERE id = :id";
        $stmt = $this->db_connection->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data): void
    {
        $query = "UPDATE colaborador SET nome_completo = :nome_completo, cpf = :cpf, rg = :rg, data_nascimento = :data_nascimento, genero = :genero, email_pessoal = :email_pessoal, telefone_celular = :telefone_celular, logradouro = :logradouro, numero = :numero, complemento = :complemento, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep, cargo = :cargo, departamento = :departamento, salario = :salario, data_admissao = :data_admissao, data_desligamento = :data_desligamento, tipo_contrato = :tipo_contrato, email_corporativo = :email_corporativo, status = :status, updated_at = :updated_at WHERE id = :id";
        $stmt = $this->db_connection->prepare($query);
        $stmt->bindValue(':id', $id);
        $this->binds($stmt, $data);
        $stmt->bindValue(':updated_at', $data['updated_at']);
        $stmt->execute();
    }

    public function softDelete($id): void
    {
        $query = "UPDATE colaborador SET status = 'Inativo' WHERE id = :id";
        $stmt = $this->db_connection->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
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
    /**
     * Busca um colaborador pelo ID.
     * @param int $id O ID do colaborador.
     * @return array|false Retorna um array com os dados ou false se não encontrar.
     */
    public function findById(int $id)
    {
        $sql = "SELECT * FROM colaborador WHERE id_colaborador = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // fetch() retorna false se não encontrar
    }
}