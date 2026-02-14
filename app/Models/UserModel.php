<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class UserModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, password_hash, role, manager_id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, role, manager_id, work_area, role_description FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function updateProfile(int $userId, string $workArea, string $roleDescription): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET work_area = :work_area, role_description = :role_description WHERE id = :id');
        $stmt->execute([
            'work_area' => $workArea,
            'role_description' => $roleDescription,
            'id' => $userId,
        ]);
    }

    public function isManagerOf(int $managerId, int $employeeId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE id = :employee_id AND manager_id = :manager_id LIMIT 1');
        $stmt->execute([
            'employee_id' => $employeeId,
            'manager_id' => $managerId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function updateWorkArea(int $userId, string $workArea): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET work_area = :work_area WHERE id = :id');
        $stmt->execute([
            'work_area' => $workArea,
            'id' => $userId,
        ]);
    }
}