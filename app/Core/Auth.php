<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\UserModel;

class Auth
{
    private const SESSION_USER_ID = 'auth_user_id';

    public function __construct(private readonly UserModel $users)
    {
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        Session::regenerate();
        Session::set(self::SESSION_USER_ID, (int) $user['id']);

        return true;
    }

    public function user(): ?array
    {
        $userId = (int) Session::get(self::SESSION_USER_ID, 0);
        if ($userId <= 0) {
            return null;
        }

        return $this->users->findById($userId);
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function logout(): void
    {
        Session::remove(self::SESSION_USER_ID);
        Session::destroy();
    }

    public function authorizeRole(array $user, array $allowedRoles): bool
    {
        return in_array($user['role'], $allowedRoles, true);
    }

    public function canViewEmployeeData(array $viewer, int $targetUserId): bool
    {
        if ((int) $viewer['id'] === $targetUserId) {
            return true;
        }

        if ($viewer['role'] === 'admin') {
            return true;
        }

        if ($viewer['role'] === 'manager') {
            return $this->users->isManagerOf((int) $viewer['id'], $targetUserId);
        }

        return false;
    }

    public function updateWorkArea(int $userId, string $workArea): void
    {
        $this->users->updateWorkArea($userId, $workArea);
        // Atualiza a sessão se o usuário logado for o mesmo que está sendo atualizado
        $currentUser = $this->user();
        if ($currentUser && (int)$currentUser['id'] === $userId) {
             // Força recarregar usuário na próxima chamada ou atualiza se possível
        }
    }

    public function updateProfile(int $userId, string $workArea, string $roleDescription): void
    {
        $this->users->updateProfile($userId, $workArea, $roleDescription);
    }
}