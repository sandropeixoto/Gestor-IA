<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class ReportModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByUserAndMonth(int $userId, string $monthYear): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, month_year, content_draft, status, submission_date, created_at, updated_at
             FROM reports
             WHERE user_id = :user_id AND month_year = :month_year
             LIMIT 1'
        );

        $stmt->execute([
            'user_id' => $userId,
            'month_year' => $monthYear,
        ]);

        $report = $stmt->fetch();

        return $report ?: null;
    }

    public function ensureMonthlyReportForUser(int $userId, string $monthYear): array
    {
        $existing = $this->findByUserAndMonth($userId, $monthYear);
        if ($existing !== null) {
            return $existing;
        }

        $insert = $this->pdo->prepare(
            'INSERT INTO reports (user_id, month_year, content_draft, status)
             VALUES (:user_id, :month_year, NULL, :status)'
        );

        $insert->execute([
            'user_id' => $userId,
            'month_year' => $monthYear,
            'status' => 'draft',
        ]);

        return $this->findByUserAndMonth($userId, $monthYear)
            ?? throw new \RuntimeException('Falha ao criar/recuperar relatório mensal.');
    }


    public function findById(int $reportId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, month_year, content_draft, status, submission_date, created_at, updated_at
             FROM reports
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute(['id' => $reportId]);
        $report = $stmt->fetch();

        return $report ?: null;
    }

    public function updateDraft(int $reportId, string $draftContent): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE reports
             SET content_draft = :content_draft
             WHERE id = :id'
        );

        $stmt->execute([
            'content_draft' => $draftContent,
            'id' => $reportId,
        ]);
    }

    public function submitReport(int $reportId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE reports
             SET status = :status, submission_date = NOW()
             WHERE id = :id'
        );

        $stmt->execute([
            'status' => 'submitted',
            'id' => $reportId,
        ]);
    }

}