<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\ReportModel;
use App\Models\ChatLogModel;
use App\Models\EvidenceModel;
use App\Core\Csrf;

class ReportController
{
    public function index(array $appConfig, Auth $auth, ReportModel $reports): void
    {
        $user = $auth->user();
        if (!$user) {
            App\Core\Router::redirect('/');
            exit;
        }

        // Filtros
        $status = $_GET['status'] ?? null;
        $monthYear = $_GET['period'] ?? null;
        
        // Se for employee, só vê os dele. Se for manager/admin, vê do time ou todos.
        $allReports = $reports->listHistory((int)$user['id'], $user['role'], 50);

        $pageTitle = 'Central de Relatórios';
        $csrfToken = Csrf::getToken();

        ob_start();
        require __DIR__ . '/../Views/reports/index.php';
        $slot = ob_get_clean();

        require __DIR__ . '/../Views/layouts/admin.php';
    }

    public function view(int $reportId, array $appConfig, Auth $auth, ReportModel $reports, ChatLogModel $chatLogs, EvidenceModel $evidences): void
    {
        $user = $auth->user();
        if (!$user) {
            App\Core\Router::redirect('/');
            exit;
        }

        $report = $reports->findById($reportId);
        if (!$report) {
            $_SESSION['flash_error'] = 'Relatório não encontrado ou módulo indisponível para este ID.';
            App\Core\Router::redirect('/reports');
            exit;
        }

        // Verificar permissão de visualização
        if (!$auth->canViewEmployeeData($user, (int)$report['user_id'])) {
            $_SESSION['flash_error'] = 'Você não tem permissão para visualizar este relatório.';
            App\Core\Router::redirect('/reports');
            exit;
        }

        $messages = $chatLogs->listByReport($reportId);
        $evidenceList = $evidences->listByReport($reportId);
        
        $pageTitle = "Relatório: " . $report['month_year'];
        $csrfToken = Csrf::getToken();

        ob_start();
        require __DIR__ . '/../Views/reports/view.php';
        $slot = ob_get_clean();

        require __DIR__ . '/../Views/layouts/admin.php';
    }

    public function processReview(int $reportId, Auth $auth, ReportModel $reports, \App\Models\NotificationModel $notifications): void
    {
        $viewer = $auth->user();
        if (!$viewer || ($viewer['role'] !== 'manager' && $viewer['role'] !== 'admin')) {
            App\Core\Router::redirect('/reports');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            App\Core\Router::redirect('/reports/view/' . $reportId);
            exit;
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            App\Core\Router::redirect('/reports/view/' . $reportId);
            exit;
        }

        $report = $reports->findById($reportId);
        if (!$report || !$auth->canViewEmployeeData($viewer, (int)$report['user_id'])) {
            App\Core\Router::redirect('/reports');
            exit;
        }

        $action = $_POST['action'] ?? '';
        $feedback = trim($_POST['feedback'] ?? ');

        if ($action === 'approve') {
            $reports->updateStatus($reportId, 'approved', $feedback !== '' ? $feedback : null);
            
            // Notificar Colaborador
            $notifications->create(
                (int)$report['user_id'],
                'report_approved',
                'Relatório Aprovado! 🎉',
                "Seu relatório de {$report['month_year']} foi aprovado pelo gestor.",
                "/reports/view/{$reportId}"
            );

        } elseif ($action === 'reject') {
            if ($feedback === '') {
                $_SESSION['flash_error'] = 'Você deve fornecer um feedback ao solicitar revisões.';
                App\Core\Router::redirect('/reports/view/' . $reportId);
                exit;
            }
            $reports->updateStatus($reportId, 'rejected', $feedback);

            // Notificar Colaborador
            $notifications->create(
                (int)$report['user_id'],
                'report_rejected',
                'Revisão Solicitada',
                "O gestor solicitou ajustes no seu relatório de {$report['month_year']}. Confira o feedback.",
                "/chat"
            );
        }

        App\Core\Router::redirect('/reports/view/' . $reportId);
        exit;
    }
}