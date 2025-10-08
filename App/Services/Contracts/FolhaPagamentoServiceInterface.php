<?php
// App/Services/Contracts/FolhaPagamentoServiceInterface.php

namespace App\Services\Contracts;

interface FolhaPagamentoServiceInterface
{
    /**
     * Orquestra o processamento da folha para um mês/ano específico.
     * @param int $ano
     * @param int $mes
     * @return array Um relatório com os resultados do processamento.
     */
    public function processarFolha(int $ano, int $mes): array;
}