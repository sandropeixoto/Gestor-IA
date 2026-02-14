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

    public function respond(array $history, string $userMessage, string $currentDraft, array $context = []): array
    {
        // Se não houver configuração, usa fallback
        if (empty($this->config)) {
            // Fallback simples se não injetado (para compatibilidade)
            return $this->fallbackRespond($userMessage, $currentDraft);
        }

        $workArea = $context['work_area'] ?? 'Geral';
        $insights = $context['insights'] ?? [];

        // Constrói a memória de longo prazo
        $memoryString = '';
        if (!empty($insights)) {
            $memoryString = "\nMEMÓRIA DE LONGO PRAZO (O QUE VOCÊ JÁ SABE SOBRE O USUÁRIO):\n";
            foreach ($insights as $insight) {
                $memoryString .= "- [{$insight['insight_type']}]: {$insight['content']}\n";
            }
        }

        // Define a persona baseada na área
        $personaCheck = match($workArea) {
            'TI' => 'Você é um Tech Lead experiente ajudando um desenvolvedor a relatar suas atividades técnicas. Foque em detalhes de arquitetura, código, deploys e incidentes.',
            'Jurídico' => 'Você é um assistente paralegal sênior. Foque em prazos processuais, status de contratos e conformidade legal.',
            'Financeiro' => 'Você é um analista financeiro sênior. Foque em fluxo de caixa, DRE, conformidade fiscal e orçamentos.',
            'Obras' => 'Você é um engenheiro de obras. Foque em cronograma físico-financeiro, diário de obra e gestão de fornecedores.',
            'RH' => 'Você é um especialista em RH. Foque em recrutamento, clima organizacional, treinamentos e departamento pessoal.',
            'Administrativo' => 'Você é um assistente executivo eficiente. Foque em organização, processos e gestão de rotina.',
            default => 'Você é uma IA assistente corporativa que ajuda colaboradores a redigirem relatórios mensais.'
        };

        $systemPrompt = "{$personaCheck}\n" .
            "Seu objetivo é entrevistar o usuário sobre suas atividades e, ao final de cada resposta, consolidar o texto em um formato profissional.\n" .
            "{$memoryString}\n" .
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
}           "1. PREFERÊNCIAS: Como o usuário gosta de escrever (tópicos, texto corrido, formal, informal)?\n" .
            "2. PROJETOS: Quais projetos ou iniciativas parecem ser recorrentes ou importantes?\n" .
            "3. VOCABULÁRIO: Termos técnicos ou siglas específicas que ele usa.\n\n" .
            "Retorne APENAS um JSON (sem markdown) no formato:\n" .
            "[\n  {\"type\": \"preference\", \"content\": \"...\"},\n  {\"type\": \"project\", \"content\": \"...\"}\n]";

        $payload = [
            'model' => $this->config['model'] ?? 'gpt-5-nano',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Relatório final:\n---\n{$reportContent}\n---"]
            ],
            'temperature' => 0.5
        ];

        try {
            $response = $this->callApi($payload);
            $content = $response['choices'][0]['message']['content'] ?? '[]';
            $cleanContent = preg_replace('/^```json\s*|\s*```$/', '', $content);
            $decoded = json_decode($cleanContent, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        } catch (\Exception $e) {
            // Silencia erro para não bloquear o fluxo principal
            return [];
        }

        return [];
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