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

    public function getRecentLogs(int $limit = 50): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT cl.id, cl.report_id, cl.sender, cl.message, cl.created_at, u.name as user_name
             FROM chat_logs cl
             JOIN reports r ON cl.report_id = r.id
             JOIN users u ON r.user_id = u.id
             ORDER BY cl.created_at DESC
             LIMIT :limit'
        );

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }
}