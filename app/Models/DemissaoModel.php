<?php

require_once BASE_PATH . '/app/Core/Model.php';

class DemissaoModel extends Model
{
    

    public function processarDemissao($funcionarioId, $dataDemissao, $tipoDemissao, $motivo, $possuiFeriasVencidas = false)
    {
        require_once BASE_PATH . '/app/Models/FuncionarioModel.php';
        $funcionarioModel = new FuncionarioModel();
        $funcionario = $funcionarioModel->getById($funcionarioId);
        if (!$funcionario) {
            error_log("Funcionário não encontrado para demissão: ID " . $funcionarioId);
            return false;
        }

        $salario = $funcionario['salario'];
        $dataAdmissao = new DateTime($funcionario['data_admissao']);
        $dataDemissaoObj = new DateTime($dataDemissao);
        $diasTrabalhadosMes = $dataDemissaoObj->format('d');
        $saldoSalario = ($salario / 30) * $diasTrabalhadosMes;
        $avisoPrevio = ($tipoDemissao === 'sem_justa_causa') ? $salario : 0;
        $mesesTrabalhadosAno = $dataDemissaoObj->format('m');
        $decimoTerceiro = ($salario / 12) * $mesesTrabalhadosAno;
        $intervalo = $dataAdmissao->diff($dataDemissaoObj);
        $mesesTrabalhadosTotal = $intervalo->y * 12 + $intervalo->m;
        $mesesProporcionaisFerias = $mesesTrabalhadosTotal % 12;
        $feriasProporcionais = ($salario / 12) * $mesesProporcionaisFerias;
        $tercoFerias = $feriasProporcionais / 3;
        $valorFeriasVencidas = 0;
        if ($possuiFeriasVencidas) {
            $valorFeriasVencidas = $salario + ($salario / 3);
        }
        $totalRescisao = $saldoSalario + $avisoPrevio + $decimoTerceiro + $feriasProporcionais + $tercoFerias + $valorFeriasVencidas;
        $calculos = [
            'funcionario_id' => $funcionarioId, 'data_demissao' => $dataDemissao, 'tipo_demissao' => $tipoDemissao, 'motivo' => $motivo,
            'saldo_salario' => $saldoSalario, 'aviso_previo' => $avisoPrevio, 'ferias_vencidas' => $valorFeriasVencidas,
            'ferias_proporcionais' => $feriasProporcionais, 'terco_ferias' => $tercoFerias, 'decimo_terceiro_proporcional' => $decimoTerceiro,
            'valor_total_rescisao' => $totalRescisao
        ];

        $sql = "INSERT INTO demissoes (funcionario_id, data_demissao, tipo_demissao, motivo, saldo_salario, aviso_previo, ferias_vencidas, ferias_proporcionais, terco_ferias, decimo_terceiro_proporcional, valor_total_rescisao) VALUES (:funcionario_id, :data_demissao, :tipo_demissao, :motivo, :saldo_salario, :aviso_previo, :ferias_vencidas, :ferias_proporcionais, :terco_ferias, :decimo_terceiro_proporcional, :valor_total_rescisao)";

        try {
            $this->db_connection->beginTransaction();
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute($calculos);
            $funcionarioModel->updateStatus($funcionarioId, 'inativo');
            $this->db_connection->commit();

            return array_merge($calculos, [
                'nome_completo' => $funcionario['nome_completo'],
                'cargo' => $funcionario['cargo'],
                'data_admissao' => $funcionario['data_admissao']
            ]);

        } catch (Exception $e) {
            $this->db_connection->rollBack();
            error_log("Erro ao processar demissão: " . $e->getMessage());
            return false;
        }
    }

    public function getResumoPorFuncionarioId($funcionarioId)
    {
        $sql = "SELECT d.*, f.nome_completo, f.cargo, f.data_admissao 
                FROM demissoes d
                JOIN funcionarios f ON d.funcionario_id = f.id
                WHERE d.funcionario_id = :id";

        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute(['id' => $funcionarioId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar resumo de demissão: " . $e->getMessage());
            return null;
        }
    }
}