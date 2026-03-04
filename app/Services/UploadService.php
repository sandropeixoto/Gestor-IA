<?php

declare(strict_types=1);

namespace App\Services;

/**
 * UploadService - Cliente para a API PluguePlus Gerenciador de Arquivos
 */
class UploadService
{
    private const API_URL = 'https://eventossefa.com.br/gestor-ia/api-files.php';
    private const API_KEY = 'EventosSefa2026';
    private const MAX_SIZE_BYTES = 10485760; // 10MB

    /**
     * Envia um arquivo para o servidor remoto de arquivos.
     * 
     * @param array $file Dados do $_FILES
     * @param int $userId ID do usuário para organização de pastas
     * @param string $period Periodo (YYYY-MM) para organização de pastas
     * @return array Dados do upload (url, path, etc)
     */
    public function saveEvidence(array $file, int $userId, string $period): array
    {
        $this->validateUpload($file);

        // Organização de subpastas: evidencias/ID_USUARIO/YYYY-MM
        $remotePath = "evidencias/{$userId}/{$period}";

        $postData = [
            'action' => 'upload',
            'key' => self::API_KEY,
            'path' => $remotePath,
            'file' => new \CURLFile($file['tmp_name'], $file['type'], $file['name'])
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Api-Key: ' . self::API_KEY
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException("Erro na comunicação com o servidor de arquivos: {$error}");
        }

        $result = json_decode((string)$response, true);

        if ($httpCode !== 200 || !isset($result['sucesso']) || !$result['sucesso']) {
            $msg = $result['erro'] ?? 'Erro desconhecido no servidor remoto.';
            throw new \RuntimeException("Falha no upload remoto: {$msg}");
        }

        return [
            'original_name' => $file['name'],
            'stored_path' => $result['id'], // O caminho relativo retornado pela API
            'url' => $result['url'],         // A URL pública do arquivo
            'mime_type' => $file['type'],
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