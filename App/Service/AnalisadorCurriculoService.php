<?php

namespace App\Service;

// CORREÇÃO: Usando a classe do cliente correto para o pacote 'gemini-api-php/client'
use Gemini\Client;
use Gemini\Enums\MimeType;

class AnalisadorCurriculoService
{
    private Client $geminiClient;
    private string $model = 'gemini-2.5-flash';

    public function __construct(string $apiKey)
    {
        // Inicializa o cliente usando a chave de API
        $this->geminiClient = new Client(apiKey: $apiKey);
    }

    /**
     * Analisa o currículo em relação à vaga usando a API do Gemini.
     */
    public function analisar(string $textoCurriculo, string $descricaoVaga): array
    {
        // 1. O PROMPT: Construção do contexto detalhado da vaga

        $titulo = $dadosVaga['titulo_vaga'] ?? 'Não especificado';
        $requisitos = $dadosVaga['descricao_vaga'] ?? $dadosVaga['requisitos'] ?? 'Requisitos básicos não detalhados.';
        $setor = $dadosVaga['nome_setor'] ?? 'Não informado';
        $cargo_nome = $dadosVaga['nome_cargo'] ?? 'Não informado';
        $salario_base = $dadosVaga['salario_base'] ?? 'Não informado';

        $contextoVaga = "INFORMAÇÕES DETALHADAS DA VAGA:\n";
        $contextoVaga .= "- Título da Vaga: {$titulo}\n";
        $contextoVaga .= "- Setor/Departamento: {$setor}\n";
        $contextoVaga .= "- Cargo Associado: {$cargo_nome}\n";
        $contextoVaga .= "- Salário Base: R$ {$salario_base}\n";
        $contextoVaga .= "- Requisitos/Descrição: {$requisitos}\n";

        // 2. Instrução de Retorno e Dados

        $prompt = "Você é um analista de RH especialista, seu objetivo é dar o match entre o currículo e a vaga. Baseie sua análise nas 'Informações Detalhadas da Vaga' e no 'Currículo para Análise'. O seu retorno DEVE ser um objeto JSON estrito com exatamente duas chaves: 'sumario' (string) e 'nota' (integer).";

        $curriculo = "CURRÍCULO PARA ANÁLISE:\n" . $textoCurriculo;

        $instrucao = "TAREFA:\n
            1. Gere um **resumo** da adequação do candidato à vaga (máximo 4 frases), focando na experiência e qualificação em relação ao **Cargo** e **Requisitos**.
            2. Calcule uma **nota de 0 a 100** para a correspondência do currículo com os requisitos da vaga.
            3. A resposta **DEVE** ser apenas o objeto JSON.
            "
        ;

        $fullPrompt = $prompt . "\n\n" . $contextoVaga . "\n\n" . $curriculo . "\n\n" . $instrucao;

        try {
            // 2. Envia o pedido para o Gemini, forçando a resposta em JSON
            $response = $this->geminiClient->geminiPro()
                ->generateContent([
                    $fullPrompt
                ], [
                    'config' => [
                        'responseMimeType' => MimeType::APPLICATION_JSON
                    ]
                ]);

            $jsonString = $response->text;
            $resultado = json_decode($jsonString, true);

            // 3. Verifica e retorna o resultado
            if (json_last_error() !== JSON_ERROR_NONE || !isset($resultado['sumario']) || !isset($resultado['nota'])) {
                return [
                    'sucesso' => false,
                    'erro' => "A API retornou um formato JSON inválido ou incompleto: " . $jsonString
                ];
            }

            return [
                'sucesso' => true,
                'resultado' => $resultado
            ];

        } catch (\Exception $e) {
            return [
                'sucesso' => false,
                'erro' => "Erro de API do Gemini: " . $e->getMessage()
            ];
        }
    }
}