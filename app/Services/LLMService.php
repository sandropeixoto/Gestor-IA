<?php

declare(strict_types=1);

namespace App\Services;

/**
 * LLMService - Cliente para a API Google Gemini Flash
 */
class LLMService
{
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';
    private const API_KEY = 'AIzaSyDXvJ_10E6qjAbs_oceAKsJ5mT-ETi4bvk';

    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Gera uma resposta utilizando a API Gemini Flash
     */
    public function respond(array $history, string $userMessage, string $currentDraft, array $context = [], ?\App\Models\AiPersonaModel $personaModel = null): array
    {
        $workArea = $context['work_area'] ?? 'Geral';
        $insights = $context['insights'] ?? [];

        // Constrói a memória de longo prazo
        $memoryString = '';
    ...
            foreach ($insights as $insight) {
                $memoryString .= "- [{$insight['insight_type']}]: {$insight['content']}\n";
            }
        }

        // Persona dinâmica do banco de dados
        $personaCheck = 'Você é uma IA assistente corporativa que ajuda colaboradores a redigirem relatórios mensais.';
        if ($personaModel) {
            $dbPrompt = $personaModel->findByWorkArea($workArea);
            if (!$dbPrompt && $workArea !== 'Geral') {
                $dbPrompt = $personaModel->findByWorkArea('Geral');
            }
            if ($dbPrompt) {
                $personaCheck = $dbPrompt;
            }
        }

        $roleDescription = $context['role_description'] ?? '';
    ...
        $mentorPrompt = !empty($roleDescription) 
            ? "\nCONTEXTO DA FUNÇÃO: \"{$roleDescription}\"\n" 
            : "\n[MODO MENTORIA] Tente entender sutilmente as responsabilidades do usuário.\n";

        $systemPrompt = "{$personaCheck}\n" .
            "Você é um COPILOTO de relatórios. O usuário está escrevendo manualmente o relatório e você deve ajudá-lo a lembrar de detalhes, corrigir gramática ou sugerir parágrafos baseados no contexto.\n" .
            "DIRETRIZES:\n" .
            "1. NÃO escreva o relatório inteiro. Apenas dê dicas ou sugira pequenos trechos.\n" .
            "2. Analise o rascunho atual para encontrar lacunas ou inconsistências.\n" .
            "3. Use a MEMÓRIA DE LONGO PRAZO para lembrar o usuário de projetos que ele mencionou no passado.\n" .
            "{$memoryString}\n" .
            "{$mentorPrompt}\n" .
            "TEXTO ATUAL DO EDITOR (RASCUNHO DO USUÁRIO):\n---\n{$currentDraft}\n---\n" .
            "RESPOSTA OBRIGATÓRIA EM JSON:\n" .
            "{\n  \"assistant_message\": \"sua fala de ajuda ou pergunta ao usuário\",\n  \"suggested_snippet\": \"um parágrafo ou lista sugerida para ele COPIAR e COLAR no texto principal (opcional, deixe vazio se não houver sugestão de texto)\"\n}";

        // Formata histórico para o padrão Gemini (Contents/Parts)
        $contents = [];
        
        // Injeta System Prompt como primeira instrução
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => "INSTRUÇÃO DE SISTEMA: {$systemPrompt}"]]
        ];
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => "Entendido. Vou atuar conforme as diretrizes e responder apenas em JSON."]]
        ];

        // Adiciona histórico recente
        $limit = $this->config['context_limit'] ?? 10;
        $recentHistory = array_slice($history, -$limit);
        foreach ($recentHistory as $msg) {
            $contents[] = [
                'role' => ($msg['sender'] === 'user') ? 'user' : 'model',
                'parts' => [['text' => $msg['message']]]
            ];
        }

        // Mensagem atual
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]]
        ];

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 2048,
                'responseMimeType' => 'application/json'
            ]
        ];

        try {
            $response = $this->callGemini($payload);
            $content = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['assistant_message'])) {
                return $decoded;
            }

            return [
                'assistant_message' => $content,
                'content_draft' => $currentDraft . "\n- " . $userMessage
            ];
        } catch (\Exception $e) {
            return $this->fallbackRespond($userMessage, $currentDraft);
        }
    }

    /**
     * Extrai insights utilizando a API Gemini Flash
     */
    public function extractInsights(string $reportContent): array
    {
        $prompt = "Analise o relatório abaixo e extraia aprendizados sobre o usuário (PREFERÊNCIAS, PROJETOS, VOCABULÁRIO).\n" .
            "Retorne APENAS um JSON no formato: [{\"type\": \"preference\", \"content\": \"...\"}]\n\n" .
            "Relatório:\n{$reportContent}";

        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => ['responseMimeType' => 'application/json']
        ];

        try {
            $response = $this->callGemini($payload);
            $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
            return json_decode($text, true) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function callGemini(array $payload): array
    {
        $ch = curl_init(self::API_URL . '?key=' . self::API_KEY);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException("Erro Gemini API (Code $httpCode): $result");
        }

        return json_decode((string)$result, true);
    }

    private function fallbackRespond(string $userMessage, string $currentDraft): array
    {
        return [
            'assistant_message' => 'Estou ajustando meus circuitos, mas anotei o que você disse.',
            'content_draft' => $currentDraft . "\n- " . $userMessage
        ];
    }

    /* 
     * MÉTODOS ARQUIVADOS (LEGADO)
     * Mantidos para referência futura se necessário retornar ao padrão OpenAI.
     * private function callOldApi(array $payload) { ... }
     */
}