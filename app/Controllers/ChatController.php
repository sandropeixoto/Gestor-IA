<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Http\JsonResponse;
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

        $csrfToken = Csrf::getToken();

        require __DIR__ . '/../Views/chat/index.php';
    }

    public function send(Auth $auth, ReportModel $reports, ChatLogModel $chatLogs, LLMService $llm, \App\Models\UserInsightModel $insights): void
    {
        $user = $auth->user();
        if (!$user) {
            JsonResponse::unauthorized();
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            JsonResponse::forbidden('Token CSRF inválido.');
        }

        $message = trim((string)($_POST['message'] ?? ''));
        if ($message === '') {
            JsonResponse::error('Mensagem obrigatória', 422);
        }

        $monthYear = date('Y-m');
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $monthYear);

        if ($report['status'] !== 'draft') {
            JsonResponse::error('Relatório já foi enviado e não pode ser editado.', 409);
        }

        $chatLogs->create((int)$report['id'], 'user', $message);
        $history = $chatLogs->listByReport((int)$report['id']);

        // Fetch User Context
        $userContext = [
            'work_area' => $user['work_area'] ?? 'Geral',
            'insights' => $insights->findByUserId((int)$user['id'], 5) // Fetch top 5 recent insights
        ];

        $llmOutput = $llm->respond($history, $message, (string)($report['content_draft'] ?? ''), $userContext);

        $reports->updateDraft((int)$report['id'], $llmOutput['content_draft']);
        $chatLogs->create((int)$report['id'], 'ai', $llmOutput['assistant_message']);

        $updatedReport = $reports->findById((int)$report['id']);

        JsonResponse::ok([
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
        $user = $auth->user();
        if (!$user) {
            JsonResponse::unauthorized();
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            JsonResponse::forbidden('Token CSRF inválido.');
        }

        $monthYear = date('Y-m');
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $monthYear);
        if ($report['status'] !== 'draft') {
            JsonResponse::error('Relatório já foi enviado e não pode receber anexos.', 409);
        }

        if (!isset($_FILES['evidence'])) {
            JsonResponse::error('Arquivo de evidência é obrigatório.', 422);
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

            JsonResponse::ok([
                'message' => 'Evidência enviada com sucesso.',
                'file_name' => $uploadData['original_name'],
                'file_path' => $uploadData['relative_path'],
                'description' => $description,
            ]);
        }
        catch (\RuntimeException $exception) {
            JsonResponse::error($exception->getMessage(), 422);
        }
    }

    public function submit(Auth $auth, ReportModel $reports): void
    {
        $user = $auth->user();
        if (!$user) {
            JsonResponse::unauthorized();
        }

        // Submit likely also needs CSRF if called via POST
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            JsonResponse::forbidden('Token CSRF inválido.');
        }

        $monthYear = date('Y-m');
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $monthYear);

        if ($report['status'] !== 'draft') {
            JsonResponse::error('Relatório já foi enviado.', 409);
        }

        $reports->submitReport((int)$report['id']);

        JsonResponse::ok(['status' => 'submitted']);
    }
}       $extractedInsights = $llm->extractInsights((string)($report['content_draft'] ?? ''));

        foreach ($extractedInsights as $insight) {
            if (isset($insight['type'], $insight['content'])) {
                $insights->create((int)$user['id'], $insight['type'], $insight['content']);
            }
        }

        JsonResponse::ok(['status' => 'submitted', 'insights_generated' => count($extractedInsights)]);
    }
}