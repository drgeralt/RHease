<?php

namespace App\Model;

// No futuro, esta classe terá a lógica para interagir com o banco de dados.
// Ex: salvar, buscar e validar os registros da tabela 'folha_ponto'.

class RegistroPonto
{
    // Atributos que espelham as colunas da tabela 'folha_ponto'
    public $id_registro;
    public $id_colaborador;
    public $timestamp_batida;
    public $geolocalizacao;
    public $caminho_foto;
    public $ip_address;

    public function __construct()
    {
        // Construtor da classe.
        // Aqui poderá entrar a conexão com o banco de dados no futuro.
    }

    /**
     * Método de exemplo para salvar um registro no banco de dados.
     * (Ainda não funcional, apenas para estrutura)
     */
    public function salvar()
    {
        // Lógica para executar um INSERT na tabela 'folha_ponto' virá aqui.
        // Por enquanto, apenas retorna true.
        return true;
    }
}
