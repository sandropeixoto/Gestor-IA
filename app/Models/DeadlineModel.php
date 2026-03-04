<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class DeadlineModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function setDeadline(int $managerId, string $monthYear, string $date): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO deadlines (manager_id, month_year, deadline_date) 
             VALUES (:manager_id, :month_year, :deadline_date)
             ON DUPLICATE KEY UPDATE deadline_date = :deadline_date'
        );

        $stmt->execute([
            'manager_id' => $managerId,
            'month_year' => $monthYear,
            'deadline_date' => $date
        ]);
    }

    public function getDeadlineForManager(int $managerId, string $monthYear): ?string
    {
        $stmt = $this->pdo->prepare(
            'SELECT deadline_date FROM deadlines 
             WHERE manager_id = :manager_id AND month_year = :month_year 
             LIMIT 1'
        );
        $stmt->execute([
            'manager_id' => $managerId,
            'month_year' => $monthYear
        ]);
        $res = $stmt->fetchColumn();
        return $res ?: null;
    }

    public function getDeadlineForEmployee(int $employeeId, string $monthYear): ?string
    {
        $stmt = $this->pdo->prepare(
            'SELECT d.deadline_date 
             FROM deadlines d
             JOIN users u ON d.manager_id = u.manager_id
             WHERE u.id = :employee_id AND d.month_year = :month_year 
             LIMIT 1'
        );
        $stmt->execute([
            'employee_id' => $employeeId,
            'month_year' => $monthYear
        ]);
        $res = $stmt->fetchColumn();
        return $res ?: null;
    }

    public function isExpired(int $employeeId, string $monthYear): bool
    {
        $deadline = $this->getDeadlineForEmployee($employeeId, $monthYear);
        if (!$deadline) return false;

        return strtotime($deadline . ' 23:59:59') < time();
    }
}