<?php

declare(strict_types = 1)
;

namespace App\Services;

class LLMService
{
    /**
     * MVP fallback: heurística local para simular entrevistadora corporativa.
     * Futuro: integrar cURL com OpenAI/Gemini/Anthropic usando chave em .env.
     */
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function respond(array $history, string $userMessage, string $currentDraft): array
    {
        // Se não houver configuração, usa fallback
        if (empty($this->config)) {
            // Fallback simples se não injetado (para compatibilidade)
            return $this->fallbackRespond($userMessage, $currentDraft);
        }

        $systemPrompt = "Você é uma IA assistente corporativa que ajuda colaboradores a redigirem relatórios mensais.\n" .
            "Seu objetivo é entrevistar o usuário sobre suas atividades e, ao final de cada resposta, consolidar o texto em um formato profissional.\n" .
            "Mantenha um tom profissional e direto. Faça perguntas curtas para extrair mais detalhes se necessário.\n" .
            "O conteúdo atual do rascunho é:\n---\n{$currentDraft}\n---\n" .
            "Retorne um JSON com duas chaves: 'assistant_message' preenchido com sua resposta ao usuário, e 'content_draft' com o texto do relatório atualizado e melhorado.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Adiciona histórico recente (limitado)
        $limit = $this->config['context_limit'] ?? 10;
        $recentHistory = array_slice($history, -$limit);

        foreach ($recentHistory as $msg) {
            $role = ($msg['sender'] === 'user') ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $msg['message']];
        }

        // Adiciona mensagem atual
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $payload = [
            'model' => $this->config['model'] ?? 'gpt-5-nano',
            'messages' => $messages,
            'temperature' => 0.7
        ];

        $response = $this->callApi($payload);

        // Tenta extrair JSON da resposta se o modelo não retornar JSON puro
        $content = $response['choices'][0]['message']['content'] ?? '';

        // Remove markdown code blocks se houver
        $cleanContent = preg_replace('/^```json\s*|\s*```$/', '', $content);

        $decoded = json_decode($cleanContent, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['assistant_message'], $decoded['content_draft'])) {
            return $decoded;
        }

        // Fallback se não retornou JSON válido
        return [
            'assistant_message' => $content,
            'content_draft' => $currentDraft . "\n- " . $userMessage . " (IA não conseguiu processar o rascunho corretamente)"
        ];
    }

    private function callApi(array $payload): array
    {
        $ch = curl_init($this->config['api_url']);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->config['api_key']
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($error) {
            throw new \RuntimeException("Erro na API LLM: $error");
        }

        if ($httpCode !== 200) {
            throw new \RuntimeException("Erro na API LLM (Status $httpCode): $result");
        }

        return json_decode($result, true);
    }

    private function fallbackRespond(string $userMessage, string $currentDraft): array
    {
        $validMsg = trim($userMessage);
        return [
            'assistant_message' => 'Estou sem conexão com a IA no momento, mas registrei sua entrada.',
            'content_draft' => $currentDraft . "\n- " . $validMsg
        ];
    }
}
