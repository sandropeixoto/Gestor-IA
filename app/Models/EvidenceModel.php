<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class EvidenceModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function listByReport(int $reportId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, file_name, file_path, file_type, description, uploaded_at
             FROM evidences
             WHERE report_id = :report_id
             ORDER BY uploaded_at DESC, id DESC'
        );

        $stmt->execute(['report_id' => $reportId]);

        return $stmt->fetchAll() ?: [];
    }

    public function create(int $reportId, string $fileName, string $filePath, string $fileType, ?string $description): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO evidences (report_id, file_name, file_path, file_type, description)
             VALUES (:report_id, :file_name, :file_path, :file_type, :description)'
        );

        $stmt->execute([
            'report_id' => $reportId,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'description' => $description,
        ]);
    }
}
