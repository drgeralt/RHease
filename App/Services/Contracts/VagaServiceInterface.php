<?php
//De vitória leal para rhyan
/**
 * Contrato para o serviço de gerenciamento de vagas.
 * Permite que outros módulos, como a Triagem por IA, acessem
 * informações sobre as vagas abertas.
 */
interface VagaServiceInterface {

    /**
     * Obtém todos os detalhes de uma vaga específica.
     *
     * @param int $idVaga O ID da vaga a ser buscada.
     * @return array|null Retorna um array com os dados da vaga (título, descrição, skills, etc.)
     * ou null se a vaga não for encontrada.
     */
    public function obterVagaPorId(int $idVaga): ?array;
}