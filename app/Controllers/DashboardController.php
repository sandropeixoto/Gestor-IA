<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\ReportModel;
use App\Core\Csrf;

class DashboardController
{
    public function index(array $appConfig, Auth $auth, ReportModel $reports): void
    {
        $user = $auth->user();
        if (!$user) {
            header('Location: /');
            exit;
        }

        $sampleTargetUserId = 3;
        $canViewSampleEmployee = $auth->canViewEmployeeData($user, $sampleTargetUserId);

        $currentMonthYear = date('Y-m');
        $monthlyReport = $reports->ensureMonthlyReportForUser((int)$user['id'], $currentMonthYear);

        $teamReports = [];
        if ($user['role'] === 'manager' || $user['role'] === 'admin') {
            $teamReports = $reports->getReportsByManager((int)$user['id'], $currentMonthYear);
        }

        $csrfToken = Csrf::getToken();

        require __DIR__ . '/../Views/dashboard/index.php';
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