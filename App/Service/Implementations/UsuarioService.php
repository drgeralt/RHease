<?php

namespace App\Service\Implementations;

use App\Model\CargoModel;
use App\Model\ColaboradorModel;
use App\Service\Contracts\UsuarioServiceInterface;

class UsuarioService
{
    private ColaboradorModel $colaboradorModel;
    private CargoModel $cargoModel;

    // DEPENDENCY INJECTION: Pass instances, do not create them here
    public function __construct(ColaboradorModel $colaboradorModel, CargoModel $cargoModel)
    {
        $this->colaboradorModel = $colaboradorModel;
        $this->cargoModel = $cargoModel;
    }

    public function obterUsuarioPorId(int $idUsuario): ?array
    {
        $colaborador = $this->colaboradorModel->buscarPorId($idUsuario); // Assumed method name based on previous context

        if (!$colaborador) {
            return null;
        }

        // Handle array structure return (nested or flat)
        $data = $colaborador['colaborador'] ?? $colaborador;

        return [
            'id_colaborador' => $data['id_colaborador'],
            'nome_completo' => $data['nome_completo'],
            'email_profissional' => $data['email_profissional'] ?? $data['email'],
        ];
    }

    public function obterSalarioBase(int $idUsuario): float
    {
        $colaboradorData = $this->colaboradorModel->buscarPorId($idUsuario);
        $colaborador = $colaboradorData['colaborador'] ?? $colaboradorData;

        if (!$colaborador || empty($colaborador['id_cargo'])) {
            return 0.0;
        }

        // Assuming CargoModel has a findById or similar
        $cargo = $this->cargoModel->findById($colaborador['id_cargo']);

        return (float)($cargo['salario_base'] ?? 0.0);
    }

    public function obterNumeroDependentes(int $idUsuario): int
    {
        $colaboradorData = $this->colaboradorModel->buscarPorId($idUsuario);
        $colaborador = $colaboradorData['colaborador'] ?? $colaboradorData;

        return (int)($colaborador['numero_dependentes'] ?? 0);
    }

    public function obterTipoContrato(int $idUsuario): ?string
    {
        $colaboradorData = $this->colaboradorModel->buscarPorId($idUsuario);
        $colaborador = $colaboradorData['colaborador'] ?? $colaboradorData;

        return $colaborador['tipo_contrato'] ?? null;
    }
}