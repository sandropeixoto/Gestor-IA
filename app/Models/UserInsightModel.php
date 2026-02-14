<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class UserInsightModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Recupera os insights mais recentes de um usuário
     */
    public function findByUserId(int $userId, int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, insight_type, content, created_at 
             FROM user_insights 
             WHERE user_id = :user_id 
             ORDER BY created_at DESC 
             LIMIT :limit'
        );
        
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo insight na memória do usuário
     */
    public function create(int $userId, string $type, string $content): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO user_insights (user_id, insight_type, content) 
             VALUES (:user_id, :insight_type, :content)'
        );

        $stmt->execute([
            'user_id' => $userId,
            'insight_type' => $type,
            'content' => $content,
        ]);
    }
}