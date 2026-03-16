<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\UserModel;
use App\Models\ReportModel;
use App\Models\UserInsightModel;
use App\Core\Csrf;
use App\Core\Router;
use App\Models\DeadlineModel;
use App\Models\NotificationModel;

class TeamController
{
    public function index(array $appConfig, Auth $auth, UserModel $users, DeadlineModel $deadlines): void
    {
        $user = $auth->user();
        if (!$user || !$auth->isManager($user)) {
            Router::redirect('/dashboard');
            return;
        }

        $currentMonthYear = date('Y-m');
        $team = $users->getTeamSummary((int)$user['id'], $currentMonthYear);
        $currentDeadline = $deadlines->getDeadlineForManager((int)$user['id'], $currentMonthYear);

        $pageTitle = 'Meu Time';
        $csrfToken = Csrf::getToken();

        ob_start();
        require __DIR__ . '/../app/Views/team/index.php';
        $slot = ob_get_clean();

        require __DIR__ . '/../app/Views/layouts/admin.php';
    }

    public function updateDeadline(Auth $auth, DeadlineModel $deadlines, NotificationModel $notifications, UserModel $users): void
    {
        $user = $auth->user();
        if (!$user || !$auth->isManager($user)) {
            Router::redirect('/dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
                Router::redirect('/team');
                return;
            }

            $date = $_POST['deadline_date'] ?? '';
            $monthYear = date('Y-m');

            if ($date) {
                $deadlines->setDeadline((int)$user['id'], $monthYear, $date);
                
                // Notificar todo o time
                $teamMembers = $users->getDirectReports((int)$user['id']);
                foreach ($teamMembers as $member) {
                    $notifications->create(
                        (int)$member['id'],
                        'system',
                        'Prazo de Entrega Definido',
                        "Atenção! Seu gestor definiu o prazo final para o relatório de {$monthYear} para o dia " . date('d/m/Y', strtotime($date)) . ".",
                        "/dashboard"
                    );
                }
            }
        }

        Router::redirect('/team');
        return;
    }

    public function viewUser(int $targetUserId, array $appConfig, Auth $auth, UserModel $users, ReportModel $reports, UserInsightModel $insights): void
    {
        $viewer = $auth->user();
        if (!$viewer || !$auth->canViewEmployeeData($viewer, $targetUserId)) {
            Router::redirect('/team');
            return;
        }

        $targetUser = $users->findById($targetUserId);
        if (!$targetUser) {
            Router::redirect('/team');
            return;
        }

        $recentReports = $reports->listHistory($targetUserId, 'employee', 12);
        $userInsights = $insights->findByUserId($targetUserId, 20);

        $pageTitle = "Perfil: " . $targetUser['name'];
        $csrfToken = Csrf::getToken();

        ob_start();
        require __DIR__ . '/../app/Views/team/view_user.php';
        $slot = ob_get_clean();

        require __DIR__ . '/../app/Views/layouts/admin.php';
    }

    public function insights(array $appConfig, Auth $auth, UserInsightModel $insights): void
    {
        $user = $auth->user();
        if (!$user || !$auth->isManager($user)) {
            Router::redirect('/dashboard');
            return;
        }

        $teamInsights = $insights->getAggregatedTeamInsights((int)$user['id'], 100);

        // Agrupamento simples para o dashboard
        $stats = [
            'total' => count($teamInsights),
            'types' => []
        ];

        foreach ($teamInsights as $insight) {
            $type = $insight['insight_type'] ?? 'Geral';
            $stats['types'][$type] = ($stats['types'][$type] ?? 0) + 1;
        }

        $pageTitle = 'Insights do Time';
        $csrfToken = Csrf::getToken();

        ob_start();
        require __DIR__ . '/../app/Views/team/insights.php';
        $slot = ob_get_clean();

        require __DIR__ . '/../app/Views/layouts/admin.php';
    }
}