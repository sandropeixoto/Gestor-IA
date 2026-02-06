<?php

declare(strict_types=1);

namespace App\Services;

class LLMService
{
    /**
     * MVP fallback: heurística local para simular entrevistadora corporativa.
     * Futuro: integrar cURL com OpenAI/Gemini/Anthropic usando chave em .env.
     */
    public function respond(array $history, string $userMessage, string $currentDraft): array
    {
        $trimmed = trim($userMessage);

        $newDraft = trim($currentDraft);
        if ($newDraft !== '') {
            $newDraft .= "\n";
        }
        $newDraft .= '- ' . $trimmed;

        $question = $this->nextQuestion($trimmed);

        return [
            'assistant_message' => $question,
            'content_draft' => $newDraft,
        ];
    }

    private function nextQuestion(string $userMessage): string
    {
        $lower = mb_strtolower($userMessage);

        if (str_contains($lower, 'resultado') || str_contains($lower, 'entrega')) {
            return 'Ótimo. Teve algum desafio durante essa atividade e como você mitigou?';
        }

        if (str_contains($lower, 'dificuldade') || str_contains($lower, 'desafio')) {
            return 'Entendi. Qual foi o impacto final no time ou no processo após essa ação?';
        }

        return 'Perfeito. Qual foi o resultado objetivo dessa atividade e quais próximos passos você recomenda?';
    }
}
