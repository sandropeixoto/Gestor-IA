<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\ReportModel;

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
        $monthlyReport = $reports->ensureMonthlyReportForUser((int) $user['id'], $currentMonthYear);

        require __DIR__ . '/../Views/dashboard/index.php';
    }
}
