<?php

declare(strict_types = 1)
;

namespace App\Controllers;

use App\Core\Auth;
use App\Models\ChatLogModel;
use App\Models\EvidenceModel;
use App\Models\ReportModel;
use App\Services\LLMService;
use App\Services\UploadService;

class ChatController
{
    public function index(
        array $appConfig,
        Auth $auth,
        ReportModel $reports,
        ChatLogModel $chatLogs,
        EvidenceModel $evidences
        ): void
    {
        $user = $auth->user();
        if (!$user) {
            header('Location: /');
            exit;
        }

        $currentMonthYear = date('Y-m');
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $currentMonthYear);
        $messages = $chatLogs->listByReport((int)$report['id']);
        $evidenceList = $evidences->listByReport((int)$report['id']);

        require __DIR__ . '/../Views/chat/index.php';
    }

    public function send(Auth $auth, ReportModel $reports, ChatLogModel $chatLogs, LLMService $llm): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $user = $auth->user();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Não autenticado']);
            return;
        }

        $message = trim((string)($_POST['message'] ?? ''));
        if ($message === '') {
            http_response_code(422);
            echo json_encode(['error' => 'Mensagem obrigatória']);
            return;
        }

        $monthYear = date('Y-m');
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $monthYear);

        if ($report['status'] !== 'draft') {
            http_response_code(409);
            echo json_encode(['error' => 'Relatório já foi enviado e não pode ser editado.']);
            return;
        }

        $chatLogs->create((int)$report['id'], 'user', $message);
        $history = $chatLogs->listByReport((int)$report['id']);

        $llmOutput = $llm->respond($history, $message, (string)($report['content_draft'] ?? ''));

        $reports->updateDraft((int)$report['id'], $llmOutput['content_draft']);
        $chatLogs->create((int)$report['id'], 'ai', $llmOutput['assistant_message']);

        $updatedReport = $reports->findById((int)$report['id']);

        echo json_encode([
            'assistant_message' => $llmOutput['assistant_message'],
            'content_draft' => $updatedReport['content_draft'] ?? $llmOutput['content_draft'],
            'status' => $updatedReport['status'] ?? 'draft',
            'updated_at' => $updatedReport['updated_at'] ?? null,
        ]);
    }

    public function upload(
        array $appConfig,
        Auth $auth,
        ReportModel $reports,
        EvidenceModel $evidences,
        UploadService $uploadService
        ): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $user = $auth->user();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Não autenticado']);
            return;
        }

        $monthYear = date('Y-m');
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $monthYear);
        if ($report['status'] !== 'draft') {
            http_response_code(409);
            echo json_encode(['error' => 'Relatório já foi enviado e não pode receber anexos.']);
            return;
        }

        if (!isset($_FILES['evidence'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Arquivo de evidência é obrigatório.']);
            return;
        }

        $description = trim((string)($_POST['description'] ?? ''));

        try {
            $baseUploadDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . ($appConfig['upload_dir'] ?? 'uploads');
            $uploadData = $uploadService->saveEvidence($_FILES['evidence'], $baseUploadDir, (int)$report['id']);

            $evidences->create(
                (int)$report['id'],
                $uploadData['original_name'],
                $uploadData['relative_path'],
                $uploadData['mime_type'],
                $description !== '' ? $description : null
            );

            echo json_encode([
                'message' => 'Evidência enviada com sucesso.',
                'file_name' => $uploadData['original_name'],
                'file_path' => $uploadData['relative_path'],
                'description' => $description,
            ]);
        }
        catch (\RuntimeException $exception) {
            http_response_code(422);
            echo json_encode(['error' => $exception->getMessage()]);
        }
    }

    public function submit(Auth $auth, ReportModel $reports): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $user = $auth->user();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Não autenticado']);
            return;
        }

        $monthYear = date('Y-m');
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $monthYear);

        if ($report['status'] !== 'draft') {
            http_response_code(409);
            echo json_encode(['error' => 'Relatório já foi enviado.']);
            return;
        }

        $reports->submitReport((int)$report['id']);

        echo json_encode(['status' => 'submitted']);
    }
}
