<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\ReportModel;
use App\Core\Csrf;

class DashboardController
{
    public function index(array $appConfig, Auth $auth, ReportModel $reports, \App\Models\DeadlineModel $deadlines): void
    {
        $user = $auth->user();
        if (!$user) {
            header('Location: /');
            exit;
        }

        $currentMonthYear = date('Y-m');
        $monthlyReport = $reports->ensureMonthlyReportForUser((int)$user['id'], $currentMonthYear);
        
        // Novos indicadores e histórico
        $stats = $reports->getDashboardStats((int)$user['id'], $user['role']);
        $recentReports = $reports->listHistory((int)$user['id'], $user['role'], 5);
        
        // Carregar Prazo
        $deadline = $deadlines->getDeadlineForEmployee((int)$user['id'], $currentMonthYear);
        $isExpired = $deadlines->isExpired((int)$user['id'], $currentMonthYear);

        $teamReports = [];
        if ($user['role'] === 'manager' || $user['role'] === 'admin') {
            $teamReports = $reports->getReportsByManager((int)$user['id'], $currentMonthYear);
        }

        $csrfToken = Csrf::getToken();
        $pageTitle = 'Dashboard';

        // Captura o conteúdo da view para o slot do layout
        ob_start();
        require __DIR__ . '/../Views/dashboard/index.php';
        $slot = ob_get_clean();

        require __DIR__ . '/../Views/layouts/admin.php';
    }
    public function updateWorkArea(Auth $auth): void
    {
        $user = $auth->user();
        if (!$user) {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
                $_SESSION['flash_error'] = 'Erro de segurança (CSRF). Tente novamente.';
                header('Location: /dashboard');
                exit;
            }

            $workArea = trim($_POST['work_area'] ?? '');
            $validAreas = ['TI', 'Administrativo', 'Financeiro', 'Jurídico', 'RH', 'Obras', 'Geral'];

            if (in_array($workArea, $validAreas, true)) {
                $auth->updateWorkArea($user['id'], $workArea);
                $_SESSION['flash_success'] = 'Área de atuação atualizada com sucesso!';
            }
            else {
                $_SESSION['flash_error'] = 'Área de atuação inválida.';
            }

            header('Location: /dashboard');
            exit;
        }
    }
}