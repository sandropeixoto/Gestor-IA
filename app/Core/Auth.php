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

    public function userModel(): UserModel
    {
        return $this->users;
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

    public function loginViaSso(string $payloadBase64, string $signature): bool
    {
        $secret = $_ENV['SSO_SECRET_KEY'] ?? '';
        if (empty($secret)) {
            return false;
        }

        // 1. Validar Assinatura
        $expectedSignature = hash_hmac('sha256', $payloadBase64, $secret);
        if (!hash_equals($expectedSignature, $signature)) {
            return false;
        }

        // 2. Decodificar e Validar Expiração
        $payloadJson = base64_decode($payloadBase64, true);
        if ($payloadJson === false) {
            return false;
        }

        $userData = json_decode($payloadJson, true);
        if (!isset($userData['user_email'], $userData['exp'])) {
            return false;
        }

        if (time() > $userData['exp']) {
            return false;
        }

        // 3. Provisionamento JIT (Just-in-Time)
        $user = $this->users->findByEmail($userData['user_email']);
        if (!$user) {
            // No novo modelo, apenas Admin (level 1) é estático. 
            // Tudo o mais entra como perfil padrão (employee).
            $role = (($userData['user_level'] ?? 0) === 1) ? 'admin' : 'employee';

            $userId = $this->users->create(
                $userData['user_name'] ?? 'Usuário SSO',
                $userData['user_email'],
                $role
            );
        } else {
            $userId = (int) $user['id'];
        }

        // 4. Iniciar Sessão
        Session::regenerate();
        Session::set(self::SESSION_USER_ID, $userId);

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

    public function isManager(?array $user = null): bool
    {
        $user = $user ?? $this->user();
        if (!$user) {
            return false;
        }

        if ($user['role'] === 'admin') {
            return true;
        }

        return $this->users->isManager((int)$user['id']);
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

        // Only managers (dynamically checked) can view their subordinates
        if ($this->isManager($viewer)) {
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