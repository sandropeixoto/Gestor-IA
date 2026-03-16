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
            App\Core\Router::redirect('/');
            exit;
        }

        $currentMonthYear = date('Y-m);
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $currentMonthYear);
        $messages = $chatLogs->listByReport((int)$report['id']);
        $evidenceList = $evidences->listByReport((int)$report['id']);

        $csrfToken = Csrf::getToken();

        require __DIR__ . '/../Views/chat/index.php';
    }

    public function send(Auth $auth, ReportModel $reports, ChatLogModel $chatLogs, LLMService $llm, \App\Models\UserInsightModel $insights, \App\Models\AiPersonaModel $personas): void
    {
        $user = $auth->user();
        if (!$user) {
            JsonResponse::unauthorized();
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            JsonResponse::forbidden('Token CSRF inválido.);
        }

        $message = trim((string)($_POST['message'] ?? ''));
        $editorContent = (string)($_POST['editor_content'] ?? '); // Conteúdo atual do editor manual

        if ($message === '') {
            JsonResponse::error('Mensagem obrigatória', 422);
        }

        $monthYear = date('Y-m);
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $monthYear);

        if ($report['status'] !== 'draft') {
            JsonResponse::error('Relatório já foi enviado e não pode ser editado.', 409);
        }

        $chatLogs->create((int)$report['id'], 'user', $message);
        $history = $chatLogs->listByReport((int)$report['id']);

        // Fetch User Context
        $userContext = [
            'work_area' => $user['work_area'] ?? 'Geral',
            'role_description' => $user['role_description'] ?? '',
            'insights' => $insights->findByUserId((int)$user['id'], 5)
        ];

        // Passa o conteúdo ATUAL do editor para a IA analisar
        $llmOutput = $llm->respond($history, $message, $editorContent, $userContext, $personas);

        // Apenas registramos a resposta da IA no chat_logs com o snippet sugerido
        $chatLogs->create((int)$report['id'], 'ai', $llmOutput['assistant_message'], $llmOutput['suggested_snippet'] ?? null);

        JsonResponse::ok([
            'assistant_message' => $llmOutput['assistant_message'],
            'suggested_snippet' => $llmOutput['suggested_snippet'] ?? '',
            'status' => $report['status'] ?? 'draft',
        ]);
    }

    public function saveDraft(Auth $auth, ReportModel $reports): void
    {
        $user = $auth->user();
        if (!$user) {
            JsonResponse::unauthorized();
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            JsonResponse::forbidden('Erro CSRF);
        }

        $content = (string)($_POST['content'] ?? ');
        $monthYear = date('Y-m);
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $monthYear);

        if ($report['status'] === 'draft') {
            $reports->updateDraft((int)$report['id'], $content);
            JsonResponse::ok(['message' => 'Rascunho salvo com sucesso']);
        } else {
            JsonResponse::error('Relatório não está em modo rascunho', 403);
        }
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
            JsonResponse::forbidden('Token CSRF inválido.);
        }

        $monthYear = date('Y-m);
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $monthYear);
        if ($report['status'] !== 'draft') {
            JsonResponse::error('Relatório já foi enviado e não pode receber anexos.', 409);
        }

        if (!isset($_FILES['evidence'])) {
            JsonResponse::error('Arquivo de evidência é obrigatório.', 422);
        }

        $description = trim((string)($_POST['description'] ?? ''));

        try {
            // Upload Remoto Organizado
            $uploadData = $uploadService->saveEvidence($_FILES['evidence'], (int)$user['id'], $monthYear);

            $evidences->create(
                (int)$report['id'],
                $uploadData['original_name'],
                $uploadData['stored_path'], // Armazena o ID remoto para manipulação via API
                $uploadData['mime_type'],
                $description !== '' ? $description : null
            );

            JsonResponse::ok([
                'message' => 'Evidência enviada com sucesso.',
                'file_name' => $uploadData['original_name'],
                'url' => $uploadData['url'],
                'description' => $description,
            ]);
        }
        catch (\RuntimeException $exception) {
            JsonResponse::error($exception->getMessage(), 422);
        }
    }

    public function submit(Auth $auth, ReportModel $reports, LLMService $llm, \App\Models\UserInsightModel $insights, \App\Models\NotificationModel $notifications): void
    {
        $user = $auth->user();
        if (!$user) {
            JsonResponse::unauthorized();
        }

        // Submit likely also needs CSRF if called via POST
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            JsonResponse::forbidden('Token CSRF inválido.);
        }

        $monthYear = date('Y-m);
        $report = $reports->ensureMonthlyReportForUser((int)$user['id'], $monthYear);

        if ($report['status'] !== 'draft') {
            JsonResponse::error('Relatório já foi enviado.', 409);
        }

        $reports->submitReport((int)$report['id']);

        // Notificar Gestor
        if ($user['manager_id']) {
            $notifications->create(
                (int)$user['manager_id'],
                'report_submitted',
                'Novo Relatório Recebido',
                "O colaborador {$user['name']} enviou o relatório de {$monthYear}.",
                "/reports/view/{$report['id']}"
            );
        }

        // Trigger Learning Mode ...
        // Em produção, isso iria para uma fila (Queue). Aqui fazemos inline (pode atrasar um pouco o response)
        $extractedInsights = $llm->extractInsights((string)($report['content_draft'] ?? ''));

        foreach ($extractedInsights as $insight) {
            if (isset($insight['type'], $insight['content'])) {
                $insights->create((int)$user['id'], $insight['type'], $insight['content']);
            }
        }

        JsonResponse::ok(['status' => 'submitted', 'insights_generated' => count($extractedInsights)]);
    }
}