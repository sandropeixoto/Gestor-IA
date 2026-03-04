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
            'SELECT id, user_id, month_year, content_draft, manager_feedback, status, submission_date, created_at, updated_at
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
            'SELECT r.*, u.name as user_name 
             FROM reports r 
             JOIN users u ON r.user_id = u.id 
             WHERE r.id = :id
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
             SET content_draft = :content_draft, status = "draft"
             WHERE id = :id'
        );

        $stmt->execute([
            'content_draft' => $draftContent,
            'id' => $reportId,
        ]);
    }

    public function updateStatus(int $reportId, string $status, ?string $feedback = null): void
    {
        $sql = 'UPDATE reports SET status = :status';
        $params = ['status' => $status, 'id' => $reportId];

        if ($feedback !== null) {
            $sql .= ', manager_feedback = :feedback';
            $params['feedback'] = $feedback;
        }

        if ($status === 'submitted') {
            $sql .= ', submission_date = NOW()';
        }

        $sql .= ' WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function submitReport(int $reportId): void
    {
        $this->updateStatus($reportId, 'submitted');
    }

    public function getReportsByManager(int $managerId, string $monthYear): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.id, r.user_id, r.month_year, r.status, r.submission_date, r.updated_at, u.name as user_name, u.email as user_email
             FROM reports r
             JOIN users u ON r.user_id = u.id
             WHERE u.manager_id = :manager_id AND r.month_year = :month_year
             ORDER BY u.name ASC'
        );

        $stmt->execute([
            'manager_id' => $managerId,
            'month_year' => $monthYear,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDashboardStats(int $userId, string $role): array
    {
        $stats = [
            'total_reports' => 0,
            'submitted' => 0,
            'approved' => 0,
            'draft' => 0,
            'team_total' => 0,
            'team_submitted' => 0
        ];

        if ($role === 'employee') {
            $stmt = $this->pdo->prepare('SELECT status, COUNT(*) as total FROM reports WHERE user_id = :user_id GROUP BY status');
            $stmt->execute(['user_id' => $userId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $row) {
                $stats[$row['status']] = (int)$row['total'];
                $stats['total_reports'] += (int)$row['total'];
            }
        } elseif ($role === 'manager' || $role === 'admin') {
            // Stats do time
            $query = 'SELECT r.status, COUNT(*) as total 
                      FROM reports r 
                      JOIN users u ON r.user_id = u.id ';
            
            if ($role === 'manager') {
                $query .= 'WHERE u.manager_id = :manager_id ';
            }
            
            $query .= 'GROUP BY r.status';
            
            $stmt = $this->pdo->prepare($query);
            if ($role === 'manager') {
                $stmt->execute(['manager_id' => $userId]);
            } else {
                $stmt->execute();
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $row) {
                $stats['team_' . $row['status']] = (int)$row['total'];
                $stats['team_total'] += (int)$row['total'];
            }
            // Adiciona contagem de enviados para o indicador principal
            $stats['team_submitted'] = $stats['team_submitted'] ?? 0;
        }

        return $stats;
    }

    public function listHistory(int $userId, ?string $role = null, int $limit = 10): array
    {
        $query = 'SELECT r.*, u.name as user_name 
                  FROM reports r 
                  JOIN users u ON r.user_id = u.id ';
        
        $params = [];
        if ($role === 'employee') {
            $query .= 'WHERE r.user_id = :user_id ';
            $params[':user_id'] = $userId;
        } elseif ($role === 'manager') {
            $query .= 'WHERE u.manager_id = :mgr_id OR r.user_id = :usr_id ';
            $params[':mgr_id'] = $userId;
            $params[':usr_id'] = $userId;
        }
        
        $query .= 'ORDER BY r.month_year DESC, r.updated_at DESC LIMIT ' . (int)$limit;
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}