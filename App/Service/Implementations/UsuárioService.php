<?php

namespace App\Services;

use App\Model\CargoModel;
use App\Model\ColaboradorModel;
use App\Service\Contracts\UsuarioServiceInterface;

class UsuarioService implements UsuarioServiceInterface
{
    private ColaboradorModel $colaboradorModel;
    private CargoModel $cargoModel;

    public function __construct()
    {
        // Instancia os Models necessários quando o serviço é criado
        $this->colaboradorModel = new ColaboradorModel();
        $this->cargoModel = new CargoModel();
    }

    /**
     * {@inheritdoc}
     */
    public function obterUsuarioPorId(int $idUsuario): ?array
    {
        // Usa o ColaboradorModel para buscar os dados básicos
        $colaborador = $this->colaboradorModel->findById($idUsuario);

        if (!$colaborador) {
            return null; // Retorna null se não encontrar
        }

        // Retorna apenas os dados principais, como definido na interface
        return [
            'id_colaborador' => $colaborador['id_colaborador'],
            'nome_completo' => $colaborador['nome_completo'],
            'email_profissional' => $colaborador['email_profissional'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function obterSalarioBase(int $idUsuario): float
    {
        $colaborador = $this->colaboradorModel->findById($idUsuario);

        if (!$colaborador || $colaborador['id_cargo'] === null) {
            // Se não encontrar colaborador ou se ele não tiver cargo associado
            return 0.0;
        }

        // Busca os detalhes do cargo usando o CargoModel
        $cargo = $this->cargoModel->findById($colaborador['id_cargo']);

        if (!$cargo || !isset($cargo['salario_base'])) {
            // Se não encontrar o cargo ou a coluna salario_base
            return 0.0;
        }

        return (float)$cargo['salario_base'];
    }

    /**
     * {@inheritdoc}
     */
    public function obterNumeroDependentes(int $idUsuario): int
    {
        $colaborador = $this->colaboradorModel->findById($idUsuario);

        if (!$colaborador || !isset($colaborador['numero_dependentes'])) {
            // Se não encontrar ou a coluna não existir
            return 0;
        }

        return (int)$colaborador['numero_dependentes'];
    }

    /**
     * {@inheritdoc}
     */
    public function obterTipoContrato(int $idUsuario): ?string
    {
        $colaborador = $this->colaboradorModel->findById($idUsuario);

        if (!$colaborador || !isset($colaborador['tipo_contrato'])) {
            return null; // Retorna null se não encontrar ou a coluna não existir
        }

        return $colaborador['tipo_contrato'];
    }
}