<?php

require_once BASE_PATH . '/app/Core/Model.php';

class ApplicationModel extends Model {
      

       public function saveApplication($data): bool{
        $sql = "INSERT INTO applications (name, email, phone, years_experience, expected_salary, bio) 
                VALUES (:name, :email, :phone, :years_experience, :expected_salary, :bio)";

        try{
            $stmt = $this->db_connection->prepare($sql);

            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':years_experience', $data['years_experience']);
            $stmt->bindParam(':expected_salary', $data['expected_salary']);
            $stmt->bindParam(':bio', $data['bio']);

            return $stmt->execute();
        } catch(PDOException $e){

            error_log("Erro ao salvar a candidatura: " . $e->getMessage());
            return false;
        }
    }

    public function getApplications(): array
    {
        $sql = "SELECT * FROM applications ORDER BY submitted_at ASC";

        try{
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            error_log("Erro ao buscar as candidaturas: " . $e->getMessage());
            return [];
        }
    }

    public function getApplicationById($id){
        $sql = "SELECT * FROM applications WHERE id = :id";

        try{
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            error_log("Erro ao buscar candidato: " . $e->getMessage());
            return [];
        }
    }

    public function updateApplicationById($id, $data): bool
    {
        $sql = "UPDATE applications SET name = :name, email = :email, phone = :phone, years_experience = :years_experience, expected_salary = :expected_salary, bio = :bio WHERE id = :id";

        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':years_experience', $data['years_experience']);
            $stmt->bindParam(':expected_salary', $data['expected_salary']);
            $stmt->bindParam(':bio', $data['bio']);
            return $stmt->execute();
        }catch(PDOException $e){
            error_log("Erro ao atualizar a candidatura: " . $e->getMessage());
            return false;
        }
    }

    public function deleteApplicationById($id): bool
    {
        $sql = "DELETE FROM applications WHERE id = :id";
        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao excluir a candidatura: " . $e->getMessage());
            return false;
        }
    }


}
