<?php

require_once BASE_PATH . '/app/Core/Model.php';

class PayrollModel extends Model{
   

   public function savePayroll($data){
      $sql = "INSERT INTO payroll (name, email, position, salary)
      VALUES (:name, :email, :position, :salary)";
      
      try{
         $stmt = $this->db_connection->prepare($sql);

         $stmt->bindParam('name', $data['name']);
         $stmt->bindParam('email', $data['email']);
         $stmt->bindParam('position', $data['position']);
         $stmt->bindParam('salary', $data['salary']);

         return $stmt->execute();
      }catch(PDOException $e){
         error_log("Erro ao salvar o registro de folha de pagamento: " . $e->getMessage());
         return false;
      }
   }

   public function getPayrolls($orderBy = 'id', $orderDirection = 'DESC', $positionFilter = null, $nameSearch = null): array
   {
       $sql = "SELECT * FROM payroll";
       $conditions = [];
       $params = [];

       if ($positionFilter) {
           $conditions[] = "position = :positionFilter";
           $params[':positionFilter'] = $positionFilter;
       }

       if ($nameSearch) {
           $conditions[] = "name LIKE :nameSearch";
           $params[':nameSearch'] = '%' . $nameSearch . '%';
       }

       if (!empty($conditions)) {
           $sql .= " WHERE " . implode(" AND ", $conditions);
       }

       // Validate orderBy and orderDirection to prevent SQL injection
       $allowedOrderBy = ['id', 'name', 'email', 'position', 'salary'];
       $allowedOrderDirection = ['ASC', 'DESC'];

       $orderBy = in_array($orderBy, $allowedOrderBy) ? $orderBy : 'id';
       $orderDirection = in_array(strtoupper($orderDirection), $allowedOrderDirection) ? strtoupper($orderDirection) : 'DESC';

       $sql .= " ORDER BY $orderBy $orderDirection";

       try {
           $stmt = $this->db_connection->prepare($sql);
           $stmt->execute($params);
           return $stmt->fetchAll(PDO::FETCH_ASSOC);
       } catch (PDOException $e) {
           error_log("Erro ao buscar registros de folha de pagamento: " . $e->getMessage());
           return [];
       }
   }

   public function getPayrollById($id)
   {
       $sql = "SELECT * FROM payroll WHERE id = :id";
       try {
           $stmt = $this->db_connection->prepare($sql);
           $stmt->execute(['id' => $id]);
           return $stmt->fetch(PDO::FETCH_ASSOC);
       } catch (PDOException $e) {
           error_log("Erro ao buscar registro de folha de pagamento por ID: " . $e->getMessage());
           return null; // Or throw an exception, depending on desired error handling
       }
   }

   public function deletePayroll($id)
   {
       $sql = "DELETE FROM payroll WHERE id = :id";
       try {
           $stmt = $this->db_connection->prepare($sql);
           $stmt->bindParam(':id', $id, PDO::PARAM_INT);
           return $stmt->execute();
       } catch (PDOException $e) {
           error_log("Erro ao remover registro de folha de pagamento: " . $e->getMessage());
           return false;
       }
   }

}