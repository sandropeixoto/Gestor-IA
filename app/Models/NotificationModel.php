<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class NotificationModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(int $userId, string $type, string $title, string $message, ?string $link = null): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO notifications (user_id, type, title, message, link) 
             VALUES (:user_id, :type, :title, :message, :link)'
        );

        $stmt->execute([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link
        ]);
    }

    public function listByUser(int $userId, int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM notifications 
             WHERE user_id = :user_id 
             ORDER BY created_at DESC 
             LIMIT :limit'
        );
        
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = FALSE');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function markAsRead(int $notificationId, int $userId): void
    {
        $stmt = $this->pdo->prepare('UPDATE notifications SET is_read = TRUE WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $notificationId, 'user_id' => $userId]);
    }

    public function markAllAsRead(int $userId): void
    {
        $stmt = $this->pdo->prepare('UPDATE notifications SET is_read = TRUE WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
    }
}