<?php
//de matheus para todos menos rhyan
/**
 * Contrato para o serviço de gerenciamento de usuários.
 * Define os métodos que outros módulos podem usar para obter
 * informações de usuários/colaboradores sem conhecer a implementação interna.
 */
interface UsuarioServiceInterface {

    /**
     * Obtém os dados principais de um usuário a partir do seu ID.
     *
     * @param int $idUsuario O ID do usuário a ser buscado.
     * @return array|null Retorna um array com os dados do usuário ou null se não for encontrado.
     * Ex: ['id_usuario' => 1, 'nome_completo' => '...', 'email' => '...']
     */
    public function obterUsuarioPorId(int $idUsuario): ?array;

    /**
     * Obtém o salário base de um colaborador.
     * Essencial para o Módulo de Remuneração.
     *
     * @param int $idUsuario O ID do usuário (colaborador).
     * @return float Retorna o valor do salário base.
     */
    public function obterSalarioBase(int $idUsuario): float;

    /**
     * Obtém o número de dependentes de um colaborador para cálculo de IRRF.
     * Essencial para o Módulo de Remuneração.
     *
     * @param int $idUsuario O ID do usuário (colaborador).
     * @return int Retorna o número de dependentes.
     */
    public function obterNumeroDependentes(int $idUsuario): int;
}