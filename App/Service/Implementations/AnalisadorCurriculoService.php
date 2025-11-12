<?php
declare(strict_types=1);

namespace App\Service\Implementations;

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

class AnalisadorCurriculoService
{
    private Client $geminiClient;

    public function __construct(Client $geminiClient)
    {
        $this->geminiClient = $geminiClient;
    }

    public function analisar(string $textoCurriculo, array $dadosVaga): array
    {
        $titulo = $dadosVaga['titulo_vaga'] ?? 'Não especificado';
        $descricao = $dadosVaga['descricao_vaga'] ?? 'Não especificada.';
        $reqNecessarios = $dadosVaga['requisitos_necessarios'] ?? 'Não especificados.';
        $reqRecomendados = $dadosVaga['requisitos_recomendados'] ?? 'Não especificados';
        $reqDesejados = $dadosVaga['requisitos_desejados'] ?? 'Não especificados';

        $contextoVaga = "
--- INFORMAÇÕES DETALHADAS DA VAGA ---
Título da Vaga: {$titulo}
Descrição Geral: {$descricao}
Requisitos Obrigatórios: {$reqNecessarios}
Requisitos Recomendados: {$reqRecomendados}
Requisitos Desejáveis: {$reqDesejados}
";

        $prompt = "
Você é um analista de RH especialista. Analise o currículo em relação à vaga.
{$contextoVaga}
--- CURRÍCULO PARA ANÁLISE ---
{$textoCurriculo}
--- TAREFA ---
1.  Gere um **sumário conciso** (máximo 4 frases) sobre a aderência do candidato.
2.  Calcule uma **nota de 0 a 100** para a correspondência do currículo. Dê maior peso aos requisitos obrigatórios.
3.  A sua resposta DEVE ser um objeto JSON estrito, sem formatação extra como ```json. A resposta deve ser apenas o JSON, contendo as chaves 'sumario' (string) e 'nota' (integer).
";

        try {
            $response = $this->geminiClient
                ->generativeModel('gemini-2.5-flash')
                ->generateContent(new TextPart($prompt));

            $jsonString = $response->text();

            $startPos = strpos($jsonString, '{');
            $endPos = strrpos($jsonString, '}');

            if ($startPos !== false && $endPos !== false) {
                $cleanJsonString = substr($jsonString, $startPos, $endPos - $startPos + 1);
                $resultado = json_decode($cleanJsonString, true);
            } else {
                $resultado = json_decode($jsonString, true);
            }

            if (json_last_error() !== JSON_ERROR_NONE || !isset($resultado['sumario']) || !isset($resultado['nota'])) {
                return ['sucesso' => false, 'erro' => "A API retornou um formato JSON inválido: " . $jsonString];
            }

            return ['sucesso' => true, 'resultado' => $resultado];

        } catch (\Exception $e) {
            return ['sucesso' => false, 'erro' => "Erro de API do Gemini: " . $e->getMessage()];
        }
    }
}