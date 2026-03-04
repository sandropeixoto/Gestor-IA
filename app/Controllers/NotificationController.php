<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\NotificationModel;
use App\Http\JsonResponse;

class NotificationController
{
    public function list(Auth $auth, NotificationModel $notifications): void
    {
        $user = $auth->user();
        if (!$user) {
            JsonResponse::unauthorized();
        }

        $list = $notifications->listByUser((int)$user['id'], 10);
        $unreadCount = $notifications->getUnreadCount((int)$user['id']);

        JsonResponse::ok([
            'notifications' => $list,
            'unread_count' => $unreadCount
        ]);
    }

    public function markRead(int $id, Auth $auth, NotificationModel $notifications): void
    {
        $user = $auth->user();
        if (!$user) {
            JsonResponse::unauthorized();
        }

        $notifications->markAsRead($id, (int)$user['id']);
        JsonResponse::ok(['sucesso' => true]);
    }

    public function markAllRead(Auth $auth, NotificationModel $notifications): void
    {
        $user = $auth->user();
        if (!$user) {
            JsonResponse::unauthorized();
        }

        $notifications->markAllAsRead((int)$user['id']);
        JsonResponse::ok(['sucesso' => true]);
    }
}