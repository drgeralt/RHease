<?php


require_once BASE_PATH . '/app/Core/Model.php';

class Colaborador extends Model
{
    // O construtor da classe pai (Model) já estabelece a conexão com o banco de dados ($this->db_connection)

    /**
     * Cria um novo colaborador no banco de dados.
     * @param array $data Dados do colaborador (nome, email, cargo).
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function create($data)
    {
        $sql = "INSERT INTO colaboradores (nome, email, cargo) VALUES (:nome, :email, :cargo)";
        $stmt = $this->db_connection->prepare($sql);

        // Limpa e associa os parâmetros para segurança
        $nome = htmlspecialchars(strip_tags($data['nome']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $cargo = htmlspecialchars(strip_tags($data['cargo']));

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cargo', $cargo);

        return $stmt->execute();
    }

    /**
     * Lê um ou todos os colaboradores do banco de dados.
     * @param int|null $id O ID do colaborador. Se nulo, retorna todos.
     * @return mixed Array associativo do colaborador, ou array de todos os colaboradores, ou false.
     */
    public function read($id = null)
    {
        if ($id) {
            $sql = "SELECT * FROM colaboradores WHERE id = :id";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $sql = "SELECT * FROM colaboradores";
            $stmt = $this->db_connection->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Atualiza os dados de um colaborador.
     * @param int $id O ID do colaborador a ser atualizado.
     * @param array $data Os novos dados.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function update($id, $data)
    {
        $sql = "UPDATE colaboradores SET nome = :nome, email = :email, cargo = :cargo WHERE id = :id";
        $stmt = $this->db_connection->prepare($sql);

        // Limpa e associa os parâmetros
        $nome = htmlspecialchars(strip_tags($data['nome']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $cargo = htmlspecialchars(strip_tags($data['cargo']));
        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cargo', $cargo);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Deleta um colaborador do banco de dados.
     * @param int $id O ID do colaborador a ser deletado.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function delete($id)
    {
        $sql = "DELETE FROM colaboradores WHERE id = :id";
        $stmt = $this->db_connection->prepare($sql);

        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}