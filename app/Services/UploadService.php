<?php

declare(strict_types=1);

namespace App\Services;

class UploadService
{
    private const MAX_SIZE_BYTES = 10485760; // 10MB

    private const ALLOWED_EXTENSIONS = ['pdf', 'xlsx', 'xls', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
    ];

    public function saveEvidence(array $file, string $uploadBaseDir, int $reportId): array
    {
        $this->validateUpload($file);

        $originalName = (string) $file['name'];
        $tmpPath = (string) $file['tmp_name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new \RuntimeException('Extensão de arquivo não permitida.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = (string) $finfo->file($tmpPath);

        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new \RuntimeException('Tipo MIME de arquivo não permitido.');
        }

        $targetDir = rtrim($uploadBaseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $reportId;
        if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
            throw new \RuntimeException('Falha ao criar diretório de upload.');
        }

        $safeBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME)) ?: 'evidence';
        $storedFileName = $safeBaseName . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $storedFileName;

        if (!move_uploaded_file($tmpPath, $targetPath)) {
            throw new \RuntimeException('Falha ao mover arquivo enviado.');
        }

        return [
            'original_name' => $originalName,
            'stored_file_name' => $storedFileName,
            'stored_path' => $targetPath,
            'relative_path' => 'uploads/' . $reportId . '/' . $storedFileName,
            'mime_type' => $mimeType,
        ];
    }

    private function validateUpload(array $file): void
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new \RuntimeException('Upload inválido.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Erro durante o upload do arquivo.');
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0) {
            throw new \RuntimeException('Arquivo vazio.');
        }

        if ($size > self::MAX_SIZE_BYTES) {
            throw new \RuntimeException('Arquivo excede o limite de 10MB.');
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            throw new \RuntimeException('Arquivo temporário de upload inválido.');
        }
    }
}
