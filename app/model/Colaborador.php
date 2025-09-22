<?php

require_once BASE_PATH . '/app/Core/Model.php';

/**
 * Class Colaborador
 * Gerencia todas as operações de banco de dados para a entidade Colaborador.
 */
class Colaborador extends Model
{
    /**
     * Cria um novo colaborador no banco de dados.
     *
     * @param array $data Um array associativo contendo os dados do colaborador.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function create($data)
    {
        $sql = "INSERT INTO colaborador (
                    nome_completo, data_nascimento, CPF, RG, genero, email, 
                    telefone, data_admissao, situacao, id_cargo, id_setor, id_endereco
                ) VALUES (
                    :nome_completo, :data_nascimento, :CPF, :RG, :genero, :email, 
                    :telefone, :data_admissao, :situacao, :id_cargo, :id_setor, :id_endereco
                )";

        $stmt = $this->db_connection->prepare($sql);

        // Limpa os dados e armazena em variáveis para usar com bindParam
        $nome_completo = htmlspecialchars(strip_tags($data['nome_completo']));
        $data_nascimento = isset($data['data_nascimento']) ? htmlspecialchars(strip_tags($data['data_nascimento'])) : null;
        $CPF = htmlspecialchars(strip_tags($data['CPF']));
        $RG = htmlspecialchars(strip_tags($data['RG']));
        $genero = isset($data['genero']) ? htmlspecialchars(strip_tags($data['genero'])) : null;
        $email = htmlspecialchars(strip_tags($data['email']));
        $telefone = isset($data['telefone']) ? htmlspecialchars(strip_tags($data['telefone'])) : null;
        $data_admissao = htmlspecialchars(strip_tags($data['data_admissao']));
        $situacao = isset($data['situacao']) ? htmlspecialchars(strip_tags($data['situacao'])) : 'ativo'; // Valor padrão
        $id_cargo = isset($data['id_cargo']) ? htmlspecialchars(strip_tags($data['id_cargo'])) : null;
        $id_setor = htmlspecialchars(strip_tags($data['id_setor']));
        $id_endereco = isset($data['id_endereco']) ? htmlspecialchars(strip_tags($data['id_endereco'])) : null;

        // Associa as variáveis aos parâmetros da query
        $stmt->bindParam(':nome_completo', $nome_completo);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':CPF', $CPF);
        $stmt->bindParam(':RG', $RG);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':data_admissao', $data_admissao);
        $stmt->bindParam(':situacao', $situacao);
        $stmt->bindParam(':id_cargo', $id_cargo);
        $stmt->bindParam(':id_setor', $id_setor);
        $stmt->bindParam(':id_endereco', $id_endereco);

        return $stmt->execute();
    }

    /**
     * Lê um ou todos os colaboradores. Se um ID for fornecido, busca um único colaborador.
     * Se nenhum ID for fornecido, busca todos os colaboradores com situação 'ativo'.
     *
     * @param int|null $id O ID do colaborador a ser buscado.
     * @return mixed Retorna um array associativo para um único colaborador, um array de arrays para múltiplos, ou false se não encontrar.
     */
    public function read($id = null)
    {
        if ($id) {
            $sql = "SELECT * FROM colaborador WHERE id_colaborador = :id";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $sql = "SELECT * FROM colaborador WHERE situacao = 'ativo'";
            $stmt = $this->db_connection->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Atualiza os dados de um colaborador existente.
     *
     * @param int $id O ID do colaborador a ser atualizado.
     * @param array $data Os novos dados do colaborador.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function update($id, $data)
    {
        $sql = "UPDATE colaborador SET 
                    nome_completo = :nome_completo, data_nascimento = :data_nascimento, CPF = :CPF, 
                    RG = :RG, genero = :genero, email = :email, telefone = :telefone, 
                    data_admissao = :data_admissao, situacao = :situacao, id_cargo = :id_cargo, 
                    id_setor = :id_setor, id_endereco = :id_endereco 
                WHERE id_colaborador = :id_colaborador";

        $stmt = $this->db_connection->prepare($sql);

        // Limpa os dados e armazena em variáveis
        $nome_completo = htmlspecialchars(strip_tags($data['nome_completo']));
        $data_nascimento = isset($data['data_nascimento']) ? htmlspecialchars(strip_tags($data['data_nascimento'])) : null;
        $CPF = htmlspecialchars(strip_tags($data['CPF']));
        $RG = htmlspecialchars(strip_tags($data['RG']));
        $genero = isset($data['genero']) ? htmlspecialchars(strip_tags($data['genero'])) : null;
        $email = htmlspecialchars(strip_tags($data['email']));
        $telefone = isset($data['telefone']) ? htmlspecialchars(strip_tags($data['telefone'])) : null;
        $data_admissao = htmlspecialchars(strip_tags($data['data_admissao']));
        $situacao = isset($data['situacao']) ? htmlspecialchars(strip_tags($data['situacao'])) : 'ativo';
        $id_cargo = isset($data['id_cargo']) ? htmlspecialchars(strip_tags($data['id_cargo'])) : null;
        $id_setor = htmlspecialchars(strip_tags($data['id_setor']));
        $id_endereco = isset($data['id_endereco']) ? htmlspecialchars(strip_tags($data['id_endereco'])) : null;
        $id_colaborador = htmlspecialchars(strip_tags($id));

        // Associa as variáveis
        $stmt->bindParam(':nome_completo', $nome_completo);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':CPF', $CPF);
        $stmt->bindParam(':RG', $RG);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':data_admissao', $data_admissao);
        $stmt->bindParam(':situacao', $situacao);
        $stmt->bindParam(':id_cargo', $id_cargo);
        $stmt->bindParam(':id_setor', $id_setor);
        $stmt->bindParam(':id_endereco', $id_endereco);
        $stmt->bindParam(':id_colaborador', $id_colaborador);

        return $stmt->execute();
    }

    /**
     * Desativa um colaborador (Soft Delete), mudando sua situação para 'inativo'
     * e registrando a data de desligamento.
     *
     * @param int $id O ID do colaborador a ser desativado.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function deactivate($id)
    {
        $sql = "UPDATE colaborador 
                SET situacao = 'inativo', data_desligamento = CURDATE() 
                WHERE id_colaborador = :id";

        $stmt = $this->db_connection->prepare($sql);

        $id_colaborador = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id_colaborador);

        return $stmt->execute();
    }
}