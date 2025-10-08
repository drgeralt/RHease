<?php
declare(strict_types=1);

namespace App\Service\Implementations;

use App\Core\Database;
use App\Services\Contracts\BeneficioServiceInterface;
use PDO;
use PDOException;

class BeneficioService implements BeneficioServiceInterface {

    private PDO $db;

    public function __construct() {
        // Obtém a instância única da conexão PDO
        $this->db = Database::getInstance();
    }

    /**
     * Calcula o valor total de descontos de todos os benefícios aplicáveis.
     *
     * @inheritDoc
     */
    public function calcularTotalDescontos(int $idColaborador, int $mes, int $ano): float
    {
        $totalDescontos = 0.0;

        // 1. Consulta SQL para buscar benefícios e seus valores específicos
        $sql = "
            SELECT 
                cb.valor_especifico, 
                bc.tipo_valor,      
                bc.nome AS nome_beneficio
            FROM 
                colaborador_beneficio cb
            JOIN 
                beneficios_catalogo bc ON cb.id_beneficio = bc.id_beneficio
            WHERE 
                cb.id_colaborador = :idColaborador
        ";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idColaborador', $idColaborador, PDO::PARAM_INT);
            $stmt->execute();
            $beneficios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Erro no BD ao buscar benefícios para ID {$idColaborador}: " . $e->getMessage());
            return 0.0; 
        }

        // 2. Itera sobre os benefícios e aplica a lógica de desconto
        foreach ($beneficios as $beneficio) {
            
            $valorDesconto = 0.0;
            $tipoValor = strtoupper($beneficio['tipo_valor']);
            $valorEspecifico = (float) $beneficio['valor_especifico'];

            switch ($tipoValor) {
                case 'FIXO':
                    // Para valores fixos (como plano de saúde), usa-se o valor direto.
                    $valorDesconto = $valorEspecifico;
                    break;

                case 'PERCENTUAL':
                    // Para valores percentuais (como Vale Transporte ou porcentagem de plano de saúde).
                    // O campo 'valor_especifico' DEVE conter a porcentagem (ex: 6.0).
                    
                    $salarioBase = $this->getSalarioBase($idColaborador);
                    
                    if ($salarioBase > 0 && $valorEspecifico > 0) {
                        // Desconto = Salário Base * (Porcentagem / 100)
                        $valorDesconto = $salarioBase * ($valorEspecifico / 100);

                        // Lógica de Teto/Limite de Desconto de VT (6% do salário base, com teto legal)
                        if ($beneficio['nome_beneficio'] === 'Vale Transporte' && $valorDesconto > 150.00) {
                            // Este é um exemplo de regra de negócio, ajuste conforme o seu sistema
                            $valorDesconto = 150.00; 
                        }
                    }
                    break;
                
                case 'VARIÁVEL':
                    // Para benefícios variáveis (ex: Vale Refeição com ajuste mensal).
                    // Assume-se que 'valor_especifico' já é o valor total descontado no mês.
                    $valorDesconto = $valorEspecifico;
                    break;

                default:
                    break;
            }

            $totalDescontos += $valorDesconto;
        }

        return $totalDescontos;
    }

    /**
     * Busca o salário base do colaborador, fazendo JOIN com a tabela 'cargo'.
     *
     * @param int $idColaborador
     * @return float O salário base do colaborador.
     */
    private function getSalarioBase(int $idColaborador): float
    {
        $sql = "
            SELECT 
                c.salario_base 
            FROM 
                colaborador col
            JOIN 
                cargo c ON col.id_cargo = c.id_cargo
            WHERE 
                col.id_colaborador = :idColaborador
        ";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idColaborador', $idColaborador, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado ? (float) $resultado['salario_base'] : 0.0;
            
        } catch (PDOException $e) {
            error_log("Erro no BD ao buscar salário base para ID {$idColaborador}: " . $e->getMessage());
            return 0.0; 
        }
    }
}