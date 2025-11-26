<?php
namespace App\Model;

use App\Core\Model;
use PDO;

class EmpresaModel extends Model
{
    // Busca a empresa ativa (Sessão > Padrão > Primeira Encontrada)
    public function getEmpresaAtiva()
    {
        // 1. Tenta pegar da sessão
        if (isset($_SESSION['empresa_ativa_id'])) {
            $id = $_SESSION['empresa_ativa_id'];
            $empresa = $this->buscarPorId($id);
            if ($empresa) return $empresa;
        }

        // 2. Se não tiver na sessão, pega a marcada como padrão
        $stmt = $this->db_connection->query("SELECT * FROM empresa_perfil WHERE padrao = 1 LIMIT 1");
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empresa) {
            $_SESSION['empresa_ativa_id'] = $empresa['id_empresa']; // Salva na sessão
            return $empresa;
        }

        // 3. Se não tiver padrão, pega a primeira que achar
        $stmt = $this->db_connection->query("SELECT * FROM empresa_perfil LIMIT 1");
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empresa) {
            $_SESSION['empresa_ativa_id'] = $empresa['id_empresa'];
            return $empresa;
        }

        // 4. Retorna um dummy para não quebrar o PDF se o banco estiver vazio
        return [
            'id_empresa' => 0,
            'razao_social' => 'Empresa Não Configurada',
            'cnpj' => '00.000.000/0000-00',
            'endereco' => '', 'cidade_uf' => ''
        ];
    }

    public function listarTodas()
    {
        return $this->db_connection->query("SELECT * FROM empresa_perfil ORDER BY padrao DESC, razao_social ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db_connection->prepare("SELECT * FROM empresa_perfil WHERE id_empresa = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar($dados)
    {
        if (empty($dados['id'])) {
            // Criar
            $sql = "INSERT INTO empresa_perfil (razao_social, cnpj, endereco, cidade_uf) VALUES (:rs, :cnpj, :end, :cid)";
            $stmt = $this->db_connection->prepare($sql);
            return $stmt->execute([
                ':rs' => $dados['razao_social'],
                ':cnpj' => $dados['cnpj'],
                ':end' => $dados['endereco'],
                ':cid' => $dados['cidade_uf']
            ]);
        } else {
            // Editar
            $sql = "UPDATE empresa_perfil SET razao_social = :rs, cnpj = :cnpj, endereco = :end, cidade_uf = :cid WHERE id_empresa = :id";
            $stmt = $this->db_connection->prepare($sql);
            return $stmt->execute([
                ':rs' => $dados['razao_social'],
                ':cnpj' => $dados['cnpj'],
                ':end' => $dados['endereco'],
                ':cid' => $dados['cidade_uf'],
                ':id' => $dados['id']
            ]);
        }
    }

    public function definirComoAtiva($id)
    {
        // Verifica se existe antes de setar
        if ($this->buscarPorId($id)) {
            $_SESSION['empresa_ativa_id'] = $id;
            return true;
        }
        return false;
    }
}