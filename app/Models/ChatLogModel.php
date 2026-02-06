<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class ChatLogModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function listByReport(int $reportId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT sender, message, created_at
             FROM chat_logs
             WHERE report_id = :report_id
             ORDER BY created_at ASC, id ASC'
        );

        $stmt->execute(['report_id' => $reportId]);

        return $stmt->fetchAll() ?: [];
    }

    public function create(int $reportId, string $sender, string $message): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO chat_logs (report_id, sender, message)
             VALUES (:report_id, :sender, :message)'
        );

        $stmt->execute([
            'report_id' => $reportId,
            'sender' => $sender,
            'message' => $message,
        ]);
    }
}
