<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class AiPersonaModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByWorkArea(string $workArea): ?string
    {
        $stmt = $this->pdo->prepare('SELECT prompt FROM ai_personas WHERE work_area = :work_area LIMIT 1');
        $stmt->execute(['work_area' => $workArea]);
        $res = $stmt->fetchColumn();
        return $res ?: null;
    }

    public function listAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM ai_personas ORDER BY work_area ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $workArea, string $prompt): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ai_personas (work_area, prompt) VALUES (:work_area, :prompt)');
        $stmt->execute(['work_area' => $workArea, 'prompt' => $prompt]);
    }

    public function update(int $id, string $workArea, string $prompt): void
    {
        $stmt = $this->pdo->prepare('UPDATE ai_personas SET work_area = :work_area, prompt = :prompt WHERE id = :id');
        $stmt->execute(['work_area' => $workArea, 'prompt' => $prompt, 'id' => $id]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ai_personas WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM ai_personas WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}